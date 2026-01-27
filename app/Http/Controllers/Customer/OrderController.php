<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Orders\Order;
use App\Models\Orders\OrderLine;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderController extends Controller
{
    public function index(Customer $customer)
    {
        $orders = Order::query()
            ->where('customer_id', $customer->id)
            ->latest()
            ->get();

        return view('customers.orders.index', compact('orders', 'customer'));
    }

    /**
     * Create draft order (empty sheet) — staff intake
     */
    public function create(Customer $customer)
    {
        $order = Order::create([
            'company_id'     => $customer->company_id,
            'customer_id'    => $customer->id,
            'order_number'   => $this->generateOrderNumber(),
            'source_channel' => 'staff_intake',
            'status'         => 'draft',
            'created_by'     => Auth::id(),
        ]);

        return redirect()->route('orders.show', [$customer, $order]);
    }

    /**
     * Show order sheet (selected items only)
     */
    public function show(Customer $customer, Order $order)
    {
        $this->authorizeCustomerOrder($order, $customer);

        $order->load('orderLines.product', 'orderLines.productVariation');

        return view('customers.orders.show', compact('customer', 'order'));
    }

    public function print(Customer $customer, Order $order)
    {
        abort_unless($order->customer_id === $customer->id, 404);

        return view('customers.orders.print', [
            'customer' => $customer,
            'order' => $order,
        ]);
    }

    /**
     * Add Products screen (portfolio-only, grouped by category)
     */
    public function addProducts(Customer $customer, Order $order)
    {
        $this->authorizeCustomerOrder($order, $customer);
        $order->assertIsEditable();


        // 🔒 GUARD: customer must have portfolio products to order
        if (! $customer->productPortfolio()->where('is_active', true)->exists()) {
            return redirect()
                ->route('orders.show', [$customer, $order])
                ->with(
                    'error',
                    'No products are available for this customer. Please configure the customer portfolio first.'
                );
        }

        $order->load('orderLines');

        $today = Carbon::today();

        $portfolioEntries = $customer->productPortfolio()
            ->where('is_active', true)
            ->whereDate('effective_from', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('effective_to')
                  ->orWhereDate('effective_to', '>=', $today);
            })
            ->with([
                'product.category',
                'product.variations',
                'productVariation',
            ])
            ->get();

        $categoriesById = [];
        $productPermissions = [];

        foreach ($portfolioEntries as $entry) {
            $product = $entry->product;
            if (! $product || ! $product->category) {
                continue;
            }

            $category = $product->category;
            $catId = (int) $category->id;

            if (! isset($categoriesById[$catId])) {
                $categoriesById[$catId] = (object) [
                    'id' => $category->id,
                    'category_name' => $category->category_name ?? null,
                    'name' => $category->name ?? null,
                    'products' => collect(),
                ];
            }

            $pid = (int) $product->id;

            if (! isset($productPermissions[$pid])) {
                $productPermissions[$pid] = [
                    'allow_all' => false,
                    'allowed_variation_ids' => [],
                ];
            }

            if ($entry->product_variation_id === null) {
                $productPermissions[$pid]['allow_all'] = true;
            } else {
                $productPermissions[$pid]['allowed_variation_ids'][(int) $entry->product_variation_id] = true;
            }

            if (! $categoriesById[$catId]->products->firstWhere('id', $product->id)) {
                $categoriesById[$catId]->products->push($product);
            }
        }

        foreach ($categoriesById as $catObj) {
            $catObj->products = $catObj->products->map(function ($product) use ($productPermissions) {
                $pid = (int) $product->id;
                $perm = $productPermissions[$pid];

                if ($product->variations && $product->variations->count() > 0 && ! $perm['allow_all']) {
                    $allowedIds = array_keys($perm['allowed_variation_ids']);
                    $product->setRelation(
                        'variations',
                        $product->variations->whereIn('id', $allowedIds)->values()
                    );
                }

                return $product;
            })->values();
        }

        $categories = collect(array_values($categoriesById))
            ->filter(fn ($c) => $c->products->count() > 0)
            ->values();

        $selected = $order->orderLines->mapWithKeys(function ($l) {
            return [
                $l->product_id . ':' . ($l->product_variation_id ?? '0') => true
            ];
        })->all();

        return view('customers.orders.add-products', compact(
            'customer',
            'order',
            'categories',
            'selected'
        ));
    }

    /**
     * Save selected products/variations and their quantities
     */
    public function storeAddedProducts(Request $request, Customer $customer, Order $order)
    {
        $this->authorizeCustomerOrder($order, $customer);
$order->assertIsEditable();

        $data = $request->validate([
            'items'                         => ['array'],
            'items.*.product_id'            => ['required', 'integer'],
            'items.*.product_variation_id'  => ['nullable', 'integer'],
            'items.*.requested_quantity'    => ['nullable', 'integer', 'min:0'],
        ]);

        $normalized = [];

        foreach ($data['items'] ?? [] as $row) {
            $productId = (int) ($row['product_id'] ?? 0);
            $variationId = $row['product_variation_id'] !== '' ? (int) $row['product_variation_id'] : null;
            $qty = (int) ($row['requested_quantity'] ?? 0);

            if ($productId > 0 && $qty > 0) {
                $this->assertProductInPortfolio($customer, $productId, $variationId);

                $normalized[$productId . ':' . ($variationId ?? '0')] = [
                    'product_id' => $productId,
                    'product_variation_id' => $variationId,
                    'requested_quantity' => $qty,
                ];
            }
        }

        OrderLine::where('order_id', $order->id)->get()->each(function ($line) use ($normalized) {
            $key = $line->product_id . ':' . ($line->product_variation_id ?? '0');
            if (! isset($normalized[$key])) {
                $line->delete();
            }
        });

        foreach ($normalized as $row) {
            [$price, $vat] = $this->resolvePricingSnapshot(
                $customer,
                $row['product_id'],
                $row['product_variation_id']
            );

            OrderLine::updateOrCreate(
                [
                    'order_id' => $order->id,
                    'product_id' => $row['product_id'],
                    'product_variation_id' => $row['product_variation_id'],
                ],
                [
                    'requested_quantity' => $row['requested_quantity'],
                    'unit_price_ex_vat' => $price,
                    'vat_rate' => $vat,
                    'line_status' => 'pending',
                ]
            );
        }

        return redirect()->route('orders.show', [$customer, $order])
            ->with('success', 'Products added to order.');
    }

    public function review(Customer $customer, Order $order)
    {
        $this->authorizeCustomerOrder($order, $customer);
$order->assertIsEditable();

        $order->load('orderLines.product', 'orderLines.productVariation');

        return view('customers.orders.review', compact('customer', 'order'));
    }

    public function upsertLine(Request $request, Customer $customer, Order $order)
    {
        $this->authorizeCustomerOrder($order, $customer);
$order->assertIsEditable();

        $data = $request->validate([
            'product_id' => ['required', 'integer'],
            'product_variation_id' => ['nullable', 'integer'],
            'requested_quantity' => ['nullable', 'integer', 'min:0'],
        ]);

        $productId = (int) $data['product_id'];
        $variationId = $data['product_variation_id'] !== '' ? (int) $data['product_variation_id'] : null;
        $qty = (int) ($data['requested_quantity'] ?? 0);

        if ($qty <= 0) {
            OrderLine::where([
                'order_id' => $order->id,
                'product_id' => $productId,
                'product_variation_id' => $variationId,
            ])->delete();

            return response()->json(['status' => 'removed']);
        }

        $this->assertProductInPortfolio($customer, $productId, $variationId);

        [$price, $vat] = $this->resolvePricingSnapshot($customer, $productId, $variationId);

        OrderLine::updateOrCreate(
            [
                'order_id' => $order->id,
                'product_id' => $productId,
                'product_variation_id' => $variationId,
            ],
            [
                'requested_quantity' => $qty,
                'unit_price_ex_vat' => $price,
                'vat_rate' => $vat,
                'line_status' => 'pending',
            ]
        );

        return response()->json(['status' => 'saved']);
    }

    protected function assertProductInPortfolio(Customer $customer, int $productId, ?int $variationId): void
    {
        $today = Carbon::today();

        $query = $customer->productPortfolio()
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->whereDate('effective_from', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('effective_to')
                  ->orWhereDate('effective_to', '>=', $today);
            });

        if ($variationId === null) {
            $query->whereNull('product_variation_id');
        } else {
            $query->where(function ($q) use ($variationId) {
                $q->whereNull('product_variation_id')
                  ->orWhere('product_variation_id', $variationId);
            });
        }

        abort_unless($query->exists(), 403);
    }

    protected function resolvePricingSnapshot(Customer $customer, int $productId, ?int $variationId): array
    {
        $product = Product::find($productId);
        $variation = $variationId ? ProductVariation::find($variationId) : null;

        return [
            $this->resolveUnitPriceExVat($customer, $product, $variation),
            $this->resolveVatRate($product),
        ];
    }

    protected function resolveUnitPriceExVat(Customer $customer, Product $product, ?ProductVariation $variation): float
    {
        $today = Carbon::today();

        if ($variation) {
            $price = $customer->productPortfolio()
                ->where('product_id', $product->id)
                ->where('product_variation_id', $variation->id)
                ->where('is_active', true)
                ->whereDate('effective_from', '<=', $today)
                ->where(function ($q) use ($today) {
                    $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $today);
                })
                ->orderByDesc('effective_from')
                ->value('agreed_price');

            if (is_numeric($price)) {
                return round((float) $price, 2);
            }
        }

        $price = $customer->productPortfolio()
            ->where('product_id', $product->id)
            ->whereNull('product_variation_id')
            ->where('is_active', true)
            ->whereDate('effective_from', '<=', $today)
            ->where(function ($q) use ($today) {
                $q->whereNull('effective_to')->orWhereDate('effective_to', '>=', $today);
            })
            ->orderByDesc('effective_from')
            ->value('agreed_price');

        if (is_numeric($price)) {
            return round((float) $price, 2);
        }

        if ($variation && is_numeric($variation->standard_price)) {
            return round((float) $variation->standard_price, 2);
        }

        if (is_numeric($product->simple_price)) {
            return round((float) $product->simple_price, 2);
        }

        return 0.00;
    }

    protected function resolveVatRate(Product $product): float
    {
        if (method_exists($product, 'effectiveVatRule')) {
            $vatRule = $product->effectiveVatRule();
            if ($vatRule && is_numeric($vatRule->rate)) {
                return round((float) $vatRule->rate, 2);
            }
        }

        return 0.00;
    }

    protected function authorizeCustomerOrder(Order $order, Customer $customer): void
    {
        abort_if((int) $order->customer_id !== (int) $customer->id, 403);
    }

    protected function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(str()->random(6));
    }
}

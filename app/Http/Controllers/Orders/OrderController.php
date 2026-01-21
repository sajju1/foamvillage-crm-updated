<?php

namespace App\Http\Controllers\Orders;

use App\Http\Controllers\Controller;
use App\Models\Orders\Order;
use App\Models\Orders\OrderLine;
use App\Models\Customer\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * List orders (staff)
     */
    public function index()
    {
        $orders = Order::with('customer')
            ->latest()
            ->paginate(20);

        return view('orders.index', compact('orders'));
    }

    /**
     * TEMP bootstrap create (staff)
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_id'    => ['required', 'integer', 'exists:customers,id'],
            'internal_notes' => ['nullable', 'string'],
        ]);

        $customer = Customer::findOrFail($request->customer_id);

        $order = Order::create([
            'company_id'     => $customer->company_id,
            'customer_id'    => $customer->id,
            'order_number'   => Order::generateOrderNumber(),
            'status'         => 'draft',
            'source_channel' => 'staff_intake',
            'internal_notes' => $request->internal_notes,
            'created_by'     => Auth::id(),
        ]);

        return redirect()
            ->route('orders.edit', $order)
            ->with('success', 'Order draft created.');
    }

    /**
     * Edit order sheet (portfolio-based)
     */
    public function edit(Order $order)
    {
        if (! $order->isEditable()) {
            abort(403, 'Order cannot be edited.');
        }

        $order->load([
            'customer',
            'lines.product',
            'lines.variation',
        ]);

        // 🔑 Load customer's active portfolio products
        // Adjust relation name if yours differs
        $portfolioItems = $order->customer
            ->productPortfolio()
            ->where('is_active', 1)
            ->with([
                'product',
                'variation',
            ])
            ->get();

        return view('orders.edit', compact('order', 'portfolioItems'));
    }

    /**
     * Add line (portfolio-only enforcement)
     */
    public function addLine(Request $request, Order $order)
    {
        if (! $order->isEditable()) {
            abort(403);
        }

        $request->validate([
            'portfolio_item_id' => ['required', 'integer'],
            'requested_quantity' => ['required', 'integer', 'min:1'],
            'notes'             => ['nullable', 'string'],
        ]);

        // Ensure product belongs to customer's portfolio
        $portfolioItem = $order->customer
            ->productPortfolio()
            ->where('id', $request->portfolio_item_id)
            ->where('is_active', 1)
            ->firstOrFail();

        DB::transaction(function () use ($order, $portfolioItem, $request) {
            OrderLine::create([
                'order_id'             => $order->id,
                'product_id'           => $portfolioItem->product_id,
                'product_variation_id' => $portfolioItem->product_variation_id,
                'requested_quantity'   => $request->requested_quantity,
                'processed_quantity'   => 0,
                'notes'                => $request->notes,
            ]);
        });

        return back()->with('success', 'Product added to order.');
    }

    /**
     * Update line quantity / notes
     */
    public function updateLine(Request $request, Order $order, OrderLine $line)
    {
        if (! $order->isEditable()) {
            abort(403);
        }

        if ($line->order_id !== $order->id) {
            abort(404);
        }

        $request->validate([
            'requested_quantity' => ['required', 'integer', 'min:1'],
            'notes'              => ['nullable', 'string'],
        ]);

        $line->update([
            'requested_quantity' => $request->requested_quantity,
            'notes'              => $request->notes,
        ]);

        return back()->with('success', 'Order line updated.');
    }

    /**
     * Cancel (soft remove) line
     */
    public function cancelLine(Order $order, OrderLine $line)
    {
        if (! $order->isEditable()) {
            abort(403);
        }

        if ($line->order_id !== $order->id) {
            abort(404);
        }

        $line->update([
            'line_status' => 'cancelled',
        ]);

        return back()->with('success', 'Order line removed.');
    }

    /**
     * Submit order
     */
    public function submit(Order $order)
    {
        if (! $order->isDraft()) {
            abort(403);
        }

        if ($order->lines()->where('line_status', 'active')->count() === 0) {
            return back()->withErrors('Cannot submit an empty order.');
        }

        $order->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        return redirect()
            ->route('orders.show', $order)
            ->with('success', 'Order submitted successfully.');
    }

    /**
     * View order
     */
    public function show(Order $order)
    {
        $order->load([
            'customer',
            'lines.product',
            'lines.variation',
        ]);

        return view('orders.show', compact('order'));
    }

    /**
     * Print order sheet
     */
    public function print(Order $order)
    {
        $order->load([
            'customer',
            'lines.product',
            'lines.variation',
        ]);

        return view('orders.print', compact('order'));
    }
}

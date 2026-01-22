<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerProductPortfolio;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use Carbon\Carbon;

class CustomerPortfolioController extends Controller
{
    public function store(Request $request, Customer $customer)
    {
        $items = $request->input('items', []);

        foreach ($items as $item) {

            // ✅ HARD GUARD: skip anything that is not a real selection
            if (
                empty($item['product_id']) ||
                empty($item['pricing_type'])
            ) {
                continue;
            }

            $product = Product::find($item['product_id']);
            if (!$product) {
                continue;
            }

            $variation = null;
            if (!empty($item['product_variation_id'])) {
                $variation = ProductVariation::find($item['product_variation_id']);
            }

            // ✅ DUPLICATE PREVENTION (ADD THIS)
            $alreadyExists = CustomerProductPortfolio::where('customer_id', $customer->id)
                ->where('product_id', $product->id)
                ->where('product_variation_id', $variation?->id)
                ->where('is_active', true)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $sellableLabel = $this->buildSellableLabel($product, $variation);

            CustomerProductPortfolio::create([
                'customer_id' => $customer->id,
                'product_id' => $product->id,
                'product_variation_id' => $variation?->id,
                'sellable_label' => $sellableLabel,
                'pricing_type' => $item['pricing_type'],

                // Fixed price only when applicable
                'agreed_price' => $item['pricing_type'] === 'fixed'
                    ? ($item['agreed_price'] ?? null)
                    : null,

                'effective_from' => Carbon::now(),
                'is_active' => true,
            ]);
        }

        return redirect()
            ->back()
            ->with('success', 'Products added to customer portfolio successfully.');
    }


    private function buildSellableLabel(Product $product, ?ProductVariation $variation): string
    {
        if ($variation) {
            return sprintf(
                '%s — %s × %s × %s',
                $product->product_name,
                $variation->length,
                $variation->width,
                $variation->thickness
            );
        }

        return $product->product_name;
    }

    public function update(Request $request, $portfolioEntry)
    {
        $entry = \App\Models\Customer\CustomerProductPortfolio::findOrFail($portfolioEntry);

        // Safety: only fixed pricing can be edited
        if ($entry->pricing_type !== 'fixed') {
            return back()->with('error', 'Formula-based pricing cannot be edited.');
        }

        $validated = $request->validate([
            'agreed_price' => ['required', 'numeric', 'min:0'],
        ]);

        $entry->update([
            'agreed_price' => $validated['agreed_price'],
        ]);

        return back()->with('success', 'Customer price updated successfully.');
    }
}

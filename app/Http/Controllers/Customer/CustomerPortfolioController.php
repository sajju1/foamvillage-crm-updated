<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerProductPortfolio;
use App\Models\Product\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class CustomerPortfolioController extends Controller
{
    /**
     * Store products (fixed / variant / formula) in customer's portfolio.
     */
    public function store(Request $request, Customer $customer)
    {
        $items = $request->input('items', []);

        if (empty($items)) {
            return back()->withErrors([
                'items' => 'Please select at least one product or variant.',
            ]);
        }

        foreach ($items as $item) {

            // Hard guard
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

            /**
             * =====================================
             * FIXED / VARIANT PRICING
             * =====================================
             */
            if ($item['pricing_type'] === 'fixed') {

                // Formula products are NOT allowed here
                if ($product->product_type === 'formula_based') {
                    continue;
                }

                if (!isset($item['agreed_price'])) {
                    continue;
                }

                CustomerProductPortfolio::updateOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'product_id' => $product->id,
                        'product_variation_id' => $item['product_variation_id'] ?? null,
                    ],
                    [
                        'pricing_type' => 'fixed',
                        'agreed_price' => $item['agreed_price'],
                        'formula_pricing_mode' => null,
                        'rate_override' => null,
                        'percentage_modifier' => null,
                        'minimum_charge' => null,
                        'rounding_rule' => null,
                        'effective_from' => Carbon::now(), // ✅ REQUIRED
                        'effective_to' => null,
                        'is_active' => true,
                    ]
                );
            }

            /**
             * =====================================
             * FORMULA PRICING
             * =====================================
             */
            if ($item['pricing_type'] === 'formula') {

                // Only formula-based products allowed
                if ($product->product_type !== 'formula_based') {
                    continue;
                }

                if (empty($item['formula_pricing_mode'])) {
                    continue;
                }

                CustomerProductPortfolio::updateOrCreate(
                    [
                        'customer_id' => $customer->id,
                        'product_id' => $product->id,
                        'product_variation_id' => null,
                    ],
                    [
                        'pricing_type' => 'formula',
                        'agreed_price' => null,
                        'formula_pricing_mode' => $item['formula_pricing_mode'],
                        'rate_override' => $item['rate_override'] ?? null,
                        'percentage_modifier' => $item['percentage_modifier'] ?? null,
                        'minimum_charge' => $item['minimum_charge'] ?? null,
                        'rounding_rule' => $item['rounding_rule'] ?? null,
                        'effective_from' => Carbon::now(), // ✅ REQUIRED
                        'effective_to' => null,
                        'is_active' => true,
                    ]
                );
            }
        }

        return back()->with('success', 'Selected products added to customer portfolio.');
    }

    /**
     * Update pricing (inline edit for fixed pricing only).
     */
    public function update(Request $request, CustomerProductPortfolio $portfolio)
    {
        $request->validate([
            'agreed_price' => ['required', 'numeric', 'min:0'],
        ]);

        // Formula pricing cannot be edited inline
        if ($portfolio->pricing_type === 'formula') {
            return back()->withErrors([
                'pricing_type' => 'Formula pricing must be edited via pricing rules.',
            ]);
        }

        $portfolio->update([
            'agreed_price' => $request->agreed_price,
        ]);

        return back()->with('success', 'Customer portfolio updated successfully.');
    }

    /**
     * Deactivate portfolio entry (no deletes).
     */
    public function deactivate(CustomerProductPortfolio $portfolio)
    {
        $portfolio->update([
            'is_active' => false,
            'effective_to' => Carbon::now(),
        ]);

        return back()->with('success', 'Product removed from customer portfolio.');
    }
}

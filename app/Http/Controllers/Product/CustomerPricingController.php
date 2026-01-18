<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\CustomerProductPortfolio;
use App\Models\Product\CustomerDiscount;
use Illuminate\Http\Request;

class CustomerPricingController extends Controller
{
    public function portfolioIndex()
    {
        $portfolios = CustomerProductPortfolio::orderBy('customer_id')->get();

        return view('products.customer_pricing.index', compact('portfolios'));
    }

    public function storePortfolio(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer',
            'product_id' => 'required|integer',
            'variation_id' => 'nullable|integer',
            'agreed_price' => 'required|numeric',
            'agreed_cost' => 'nullable|numeric',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date',
            'is_active' => 'required|boolean',
        ]);

        CustomerProductPortfolio::create($validated);

        return back()->with('success', 'Customer portfolio price saved.');
    }

    public function discountsIndex()
    {
        $discounts = CustomerDiscount::orderBy('customer_id')->get();

        return view('products.customer_pricing.discounts', compact('discounts'));
    }

    public function storeDiscount(Request $request)
    {
        $validated = $request->validate([
            'customer_id' => 'required|integer',
            'product_id' => 'nullable|integer',
            'variation_id' => 'nullable|integer',
            'discount_type' => 'required|in:fixed,percentage',
            'discount_value' => 'required|numeric',
            'valid_from' => 'required|date',
            'valid_to' => 'required|date',
        ]);

        CustomerDiscount::create($validated);

        return back()->with('success', 'Customer discount saved.');
    }
}

<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\FoamPricingRule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Product\FoamType;

class ProductPricingController extends Controller
{
    /**
     * Display foam pricing rules for a rule-based product.
     */
    public function foamRules(Product $product)
    {
        // Enforce rule-based products only
        if ($product->product_type !== 'rule_based') {
            abort(404);
        }

        $rules = FoamPricingRule::where('product_id', $product->id)
            ->orderBy('foam_type')
            ->orderBy('density')
            ->get();

        return view('products.pricing.foam.index', compact('product', 'rules'));
    }

    /**
     * Show the form for creating a new foam pricing rule.
     */

    public function createFoamRule(Product $product)
    {
        if ($product->product_type !== 'rule_based') {
            abort(404);
        }

        $foamTypes = \App\Models\Product\FoamType::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('products.pricing.foam.create', [
            'product' => $product,
            'rule' => new FoamPricingRule(),
            'foamTypes' => $foamTypes,
        ]);
    }


    /**
     * Store a newly created foam pricing rule.
     */
    public function storeFoamRule(Request $request, Product $product)
    {
        if ($product->product_type !== 'rule_based') {
            abort(404);
        }

        $validated = $request->validate([
            'foam_type_id' => 'required|exists:foam_types,id',
            'density' => 'required|numeric|min:0',

            // allow empty so we can auto-fill defaults from FoamType
            'price_unit' => 'nullable|numeric|min:0',
            'cost_unit' => 'nullable|numeric|min:0',

            'calculation_formula' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $foamType = \App\Models\Product\FoamType::findOrFail($validated['foam_type_id']);

        // keep old column in sync for backward compatibility
        $validated['foam_type'] = $foamType->name;

        // auto-fill defaults if user left blank
        if (!isset($validated['price_unit']) || $validated['price_unit'] === null || $validated['price_unit'] === '') {
            $validated['price_unit'] = $foamType->default_price_unit;
        }

        if (!isset($validated['cost_unit']) || $validated['cost_unit'] === null || $validated['cost_unit'] === '') {
            $validated['cost_unit'] = $foamType->default_cost_unit;
        }

        $validated['product_id'] = $product->id;

        FoamPricingRule::create($validated);

        return redirect()
            ->route('pricing.foam.index', $product)
            ->with('success', 'Foam pricing rule created successfully.');
    }


    /**
     * Show the form for editing an existing foam pricing rule.
     */
    public function editFoamRule(Product $product, FoamPricingRule $rule)
    {
        if (
            $product->product_type !== 'rule_based' ||
            $rule->product_id !== $product->id
        ) {
            abort(404);
        }

        $foamTypes = \App\Models\Product\FoamType::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('products.pricing.foam.edit', compact('product', 'rule', 'foamTypes'));
    }


    /**
     * Update an existing foam pricing rule.
     */
    public function updateFoamRule(Request $request, Product $product, FoamPricingRule $rule)
    {
        if (
            $product->product_type !== 'rule_based' ||
            $rule->product_id !== $product->id
        ) {
            abort(404);
        }

        $validated = $request->validate([
            'foam_type_id' => 'required|exists:foam_types,id',
            'density' => 'required|numeric|min:0',

            // allow empty so we can auto-fill defaults from FoamType
            'price_unit' => 'nullable|numeric|min:0',
            'cost_unit' => 'nullable|numeric|min:0',

            'calculation_formula' => 'nullable|string',
            'status' => 'required|in:active,inactive',
        ]);

        $foamType = \App\Models\Product\FoamType::findOrFail($validated['foam_type_id']);

        // keep old column in sync for backward compatibility
        $validated['foam_type'] = $foamType->name;

        // auto-fill defaults if user left blank
        if (!isset($validated['price_unit']) || $validated['price_unit'] === null || $validated['price_unit'] === '') {
            $validated['price_unit'] = $foamType->default_price_unit;
        }

        if (!isset($validated['cost_unit']) || $validated['cost_unit'] === null || $validated['cost_unit'] === '') {
            $validated['cost_unit'] = $foamType->default_cost_unit;
        }

        $rule->update($validated);

        return redirect()
            ->route('pricing.foam.index', $product)
            ->with('success', 'Foam pricing rule updated successfully.');
    }
}

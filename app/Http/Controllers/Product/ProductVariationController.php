<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductVariationController extends Controller
{
    /**
     * Display a listing of variations for a product.
     */
    public function index(Product $product)
    {
        // Enforce variant-based products only
        if ($product->product_type !== 'variant_based') {
            abort(404);
        }

        $variations = $product->variations()
            ->orderBy('status')
            ->orderBy('variation_code')
            ->get();

        return view('products.variations.index', compact('product', 'variations'));
    }

    /**
     * Show the form for creating a new variation.
     */
    public function create(Product $product)
    {
        if ($product->product_type !== 'variant_based') {
            abort(404);
        }

        return view('products.variations.create', [
            'product' => $product,
            'variation' => new ProductVariation(),
        ]);
    }

    /**
     * Store a newly created variation in storage.
     */
    public function store(Request $request, Product $product)
    {
        if ($product->product_type !== 'variant_based') {
            abort(404);
        }

        $validated = $request->validate([
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'thickness' => 'required|numeric|min:0',
            'size_unit' => 'required|in:inch,cm',

            'colour' => 'nullable|string|max:255',
            'variation_code' => 'nullable|string|max:255',

            'standard_price' => 'required|numeric|min:0',
            'standard_cost' => 'nullable|numeric|min:0',

            'status' => 'required|in:active,inactive',
        ]);

        DB::transaction(function () use ($validated, $product) {
            $product->variations()->create($validated);
        });

        return redirect()
            ->route('products.variations.index', $product)
            ->with('success', 'Variation created successfully.');
    }

    /**
     * Show the form for editing the specified variation.
     */
    public function edit(Product $product, ProductVariation $variation)
    {
        if (
            $product->product_type !== 'variant_based' ||
            $variation->product_id !== $product->id
        ) {
            abort(404);
        }

        return view('products.variations.edit', compact('product', 'variation'));
    }

    /**
     * Update the specified variation in storage.
     */
    public function update(Request $request, Product $product, ProductVariation $variation)
    {
        if (
            $product->product_type !== 'variant_based' ||
            $variation->product_id !== $product->id
        ) {
            abort(404);
        }

        $validated = $request->validate([
            'length' => 'required|numeric|min:0',
            'width' => 'required|numeric|min:0',
            'thickness' => 'required|numeric|min:0',
            'size_unit' => 'required|in:inch,cm',

            'colour' => 'nullable|string|max:255',
            'variation_code' => 'nullable|string|max:255',

            'standard_price' => 'required|numeric|min:0',
            'standard_cost' => 'nullable|numeric|min:0',

            'status' => 'required|in:active,inactive',
        ]);

        DB::transaction(function () use ($validated, $variation) {
            $variation->update($validated);
        });

        return redirect()
            ->route('products.variations.index', $product)
            ->with('success', 'Variation updated successfully.');
    }
}

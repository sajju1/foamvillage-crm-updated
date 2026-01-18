<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\Product;
use App\Models\Product\ProductCategory;
use App\Models\Company\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of products.
     */
   public function index(\Illuminate\Http\Request $request)
{
    $query = \App\Models\Product\Product::query()
        ->with(['company', 'category'])
        ->orderBy('product_name');

    // Search (by product name)
    if ($request->filled('q')) {
        $q = trim($request->input('q'));
        $query->where('product_name', 'like', "%{$q}%");
    }

    // Category filter
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->input('category_id'));
    }

    $products = $query->paginate(20)->withQueryString();

    $categories = \App\Models\Product\ProductCategory::where('status', 'active')
        ->orderBy('name')
        ->get();

    return view('products.index', compact('products', 'categories'));
}


    /**
     * Show the form for creating a new product.
     */
    public function create()
    {
        $companies = Company::orderByDesc('is_default')
            ->orderBy('legal_name')
            ->get();

        $categories = ProductCategory::where('status', 'active')
            ->orderBy('name')
            ->get();

        // Preselect default company
        $product = new Product();
        $defaultCompany = $companies->firstWhere('is_default', true);
        if ($defaultCompany) {
            $product->company_id = $defaultCompany->id;
        }

        return view('products.create', compact('companies', 'categories', 'product'));
    }


    /**
     * Store a newly created product in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'required|exists:product_categories,id',

            'product_name' => 'required|string|max:255',
            'product_type' => 'required|in:simple,variant_based,rule_based',
            'manufacturing_type' => 'required|in:manufactured,imported',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',

            'simple_price' => 'nullable|numeric|min:0',
            'simple_cost' => 'nullable|numeric|min:0',
        ]);

        // Clean pricing for non-simple products
        if ($validated['product_type'] !== 'simple') {
            $validated['simple_price'] = null;
            $validated['simple_cost'] = null;
        }

        DB::transaction(function () use ($validated) {
            Product::create($validated);
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Product created successfully.');
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product)
    {
        $companies = Company::orderByDesc('is_default')
            ->orderBy('legal_name')
            ->get();

        $categories = ProductCategory::where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('products.edit', compact('product', 'companies', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'company_id' => 'required|exists:companies,id',
            'category_id' => 'required|exists:product_categories,id',

            'product_name' => 'required|string|max:255',
            'product_type' => 'required|in:simple,variant_based,rule_based',
            'manufacturing_type' => 'required|in:manufactured,imported',
            'status' => 'required|in:active,inactive',
            'description' => 'nullable|string',

            'simple_price' => 'nullable|numeric|min:0',
            'simple_cost' => 'nullable|numeric|min:0',
        ]);

        // Clean pricing for non-simple products
        if ($validated['product_type'] !== 'simple') {
            $validated['simple_price'] = null;
            $validated['simple_cost'] = null;
        }

        DB::transaction(function () use ($validated, $product) {
            $product->update($validated);
        });

        return redirect()
            ->route('products.index')
            ->with('success', 'Product updated successfully.');
    }
}

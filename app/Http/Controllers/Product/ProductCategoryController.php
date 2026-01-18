<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Models\Product\ProductCategory;
use App\Models\Company\Company;
use Illuminate\Http\Request;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of product categories.
     */
    public function index()
    {
        $categories = ProductCategory::with('company')
            ->orderBy('name')
            ->get();

        return view('products.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        $companies = Company::orderByDesc('is_default')
            ->orderBy('legal_name')
            ->get();

        return view('products.categories.create', [
            'category'  => new ProductCategory(),
            'companies' => $companies,
        ]);
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'name'       => 'required|string|max:255',
            'status'     => 'required|in:active,inactive',
        ]);

        ProductCategory::create($validated);

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Category created successfully.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(ProductCategory $productCategory)
    {
        $companies = Company::orderByDesc('is_default')
            ->orderBy('legal_name')
            ->get();

        return view('products.categories.edit', [
            'category'  => $productCategory,
            'companies' => $companies,
        ]);
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, ProductCategory $productCategory)
    {
        $validated = $request->validate([
            'company_id' => 'nullable|exists:companies,id',
            'name'       => 'required|string|max:255',
            'status'     => 'required|in:active,inactive',
        ]);

        $productCategory->update($validated);

        return redirect()
            ->route('product-categories.index')
            ->with('success', 'Category updated successfully.');
    }
}

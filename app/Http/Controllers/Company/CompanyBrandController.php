<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Models\Company\CompanyBrand;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyBrandController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | List brands for a company
    |--------------------------------------------------------------------------
    */
    public function index(Company $company)
    {
        $brands = $company->brands()
            ->orderByDesc('is_default_brand')
            ->orderBy('brand_name')
            ->get();

        return view('brands.index', compact('company', 'brands'));
    }

    /*
    |--------------------------------------------------------------------------
    | Show create form
    |--------------------------------------------------------------------------
    */
    public function create(Company $company)
    {
        return view('brands.create', compact('company'));
    }

    /*
    |--------------------------------------------------------------------------
    | Store brand
    |--------------------------------------------------------------------------
    */
    public function store(Request $request, Company $company)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data, $company) {
            // If default brand, unset others for this company
            if (!empty($data['is_default_brand'])) {
                $company->brands()->update(['is_default_brand' => false]);
            }

            $company->brands()->create($data);
        });

        return redirect()
            ->route('brands.index', $company)
            ->with('success', 'Brand created successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Show edit form
    |--------------------------------------------------------------------------
    */
    public function edit(Company $company, CompanyBrand $brand)
    {
        return view('brands.edit', compact('company', 'brand'));
    }

    /*
    |--------------------------------------------------------------------------
    | Update brand
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Company $company, CompanyBrand $brand)
    {
        $data = $this->validatedData($request);

        DB::transaction(function () use ($data, $company, $brand) {
            if (!empty($data['is_default_brand'])) {
                $company->brands()
                    ->where('id', '!=', $brand->id)
                    ->update(['is_default_brand' => false]);
            }

            $brand->update($data);
        });

        return redirect()
            ->route('brands.index', $company)
            ->with('success', 'Brand updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'brand_name' => 'required|string|max:255',
            'brand_email' => 'nullable|email|max:255',
            'brand_phone' => 'nullable|string|max:50',
            'brand_website' => 'nullable|string|max:255',

            'is_default_brand' => 'nullable|boolean',
            'status' => 'required|in:active,inactive',
        ]);
    }
}

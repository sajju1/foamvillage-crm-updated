<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompanyController extends Controller
{
    /**
     * Display a listing of companies.
     */
    public function index()
    {
        $companies = Company::orderByDesc('is_default')
            ->orderBy('legal_name')
            ->get();

        return view('company.index', compact('companies'));
    }

    /**
     * Show the form for creating a new company.
     */
    public function create()
    {
        return view('company.create');
    }

    /**
     * Store a newly created company in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'legal_name' => 'required|string|max:255',
            'company_number' => 'nullable|string|max:255',
            'vat_number' => 'nullable|string|max:255',

            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'address_line3' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state_region' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',

            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',

            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_sort_code' => 'nullable|string|max:255',
            'bank_iban' => 'nullable|string|max:255',
            'bank_swift_bic' => 'nullable|string|max:255',

            'status' => 'required|in:active,inactive',
            'is_default' => 'nullable|boolean',
        ]);

        $isFirstCompany = Company::count() === 0;

        // First company MUST be default
        $validated['is_default'] = $isFirstCompany
            ? true
            : ($validated['is_default'] ?? false);

        DB::transaction(function () use ($validated) {

            if ($validated['is_default']) {
                Company::where('is_default', true)
                    ->update(['is_default' => false]);
            }

            Company::create($validated);
        });

        return redirect()
            ->route('company.index')
            ->with('success', 'Company created successfully.');
    }

    /**
     * Display the specified company.
     */
    public function show(Company $company)
    {
        $company->load('brands');

        return view('company.show', compact('company'));
    }

    /**
     * Show the form for editing the specified company.
     */
    public function edit(Company $company)
    {
        return view('company.edit', compact('company'));
    }

    /**
     * Update the specified company in storage.
     */
    public function update(Request $request, Company $company)
    {
        $validated = $request->validate([
            'legal_name' => 'required|string|max:255',
            'company_number' => 'nullable|string|max:255',
            'vat_number' => 'nullable|string|max:255',

            'address_line1' => 'nullable|string|max:255',
            'address_line2' => 'nullable|string|max:255',
            'address_line3' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'state_region' => 'nullable|string|max:255',
            'postcode' => 'nullable|string|max:50',
            'country' => 'nullable|string|max:255',

            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:255',

            'bank_name' => 'nullable|string|max:255',
            'bank_account_name' => 'nullable|string|max:255',
            'bank_account_number' => 'nullable|string|max:255',
            'bank_sort_code' => 'nullable|string|max:255',
            'bank_iban' => 'nullable|string|max:255',
            'bank_swift_bic' => 'nullable|string|max:255',

            'status' => 'required|in:active,inactive',
            'is_default' => 'nullable|boolean',
        ]);

        $validated['is_default'] = $validated['is_default'] ?? false;

        DB::transaction(function () use ($validated, $company) {

            if ($validated['is_default']) {
                Company::where('id', '!=', $company->id)
                    ->update(['is_default' => false]);
            }

            $company->update($validated);
        });

        return redirect()
            ->route('company.show', $company)
            ->with('success', 'Company updated successfully.');
    }
}

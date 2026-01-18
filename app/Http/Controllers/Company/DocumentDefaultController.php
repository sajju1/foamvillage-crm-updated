<?php

namespace App\Http\Controllers\Company;

use App\Http\Controllers\Controller;
use App\Models\Company\Company;
use App\Models\Company\DocumentDefault;
use Illuminate\Http\Request;

class DocumentDefaultController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Show edit form
    |--------------------------------------------------------------------------
    */
    public function edit(Company $company, string $documentType = 'all')
    {
        $documentDefault = DocumentDefault::firstOrCreate(
            [
                'company_id' => $company->id,
                'document_type' => $documentType,
            ],
            [
                // Sensible system defaults
                'header_title_source' => 'legal_company_name',
                'header_logo_source' => 'company_logo',
                'footer_text_source' => 'legal_disclosure',

                'show_address' => true,
                'show_company_number' => true,
                'show_vat_number' => true,
                'show_bank_details' => true,
            ]
        );

        return view('document_defaults.edit', compact('company', 'documentDefault'));
    }

    /*
    |--------------------------------------------------------------------------
    | Update defaults
    |--------------------------------------------------------------------------
    */
    public function update(Request $request, Company $company, DocumentDefault $documentDefault)
    {
        $data = $this->validatedData($request);

        $documentDefault->update($data);

        return redirect()
            ->back()
            ->with('success', 'Document defaults updated successfully.');
    }

    /*
    |--------------------------------------------------------------------------
    | Validation
    |--------------------------------------------------------------------------
    */
    protected function validatedData(Request $request): array
    {
        return $request->validate([
            'header_title_source' => 'required|in:legal_company_name,brand_name,custom_text',
            'header_custom_text' => 'nullable|string|max:255',
            'header_logo_source' => 'required|in:company_logo,brand_logo,none',

            'footer_text_source' => 'required|in:legal_disclosure,custom_text,none',
            'footer_custom_text' => 'nullable|string',
            'legal_disclosure_text' => 'nullable|string',

            'show_address' => 'nullable|boolean',
            'show_company_number' => 'nullable|boolean',
            'show_vat_number' => 'nullable|boolean',
            'show_bank_details' => 'nullable|boolean',
        ]);
    }
}

@extends('layouts.app')

@section('content')

<div class="max-w-4xl mx-auto">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-2xl font-semibold text-gray-800">
                Document Defaults
            </h1>
            <p class="text-sm text-gray-500">
                Company: {{ $company->legal_name }}
            </p>
        </div>

        <a href="{{ route('company.index') }}"
           class="text-sm text-gray-600 hover:text-gray-900">
            ← Back to Companies
        </a>
    </div>

    <div class="bg-white rounded-lg shadow p-6">

        <form method="POST"
              action="{{ route('document-defaults.update', [$company, $documentDefault]) }}">
            @csrf
            @method('PUT')

            <div class="space-y-6">

                {{-- Header Title --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Header Title Source
                    </label>
                    <select name="header_title_source"
                            class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="legal_company_name"
                            {{ $documentDefault->header_title_source === 'legal_company_name' ? 'selected' : '' }}>
                            Legal Company Name
                        </option>
                        <option value="brand_name"
                            {{ $documentDefault->header_title_source === 'brand_name' ? 'selected' : '' }}>
                            Brand Name
                        </option>
                        <option value="custom_text"
                            {{ $documentDefault->header_title_source === 'custom_text' ? 'selected' : '' }}>
                            Custom Text
                        </option>
                    </select>
                </div>

                {{-- Custom Header Text --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Custom Header Text
                    </label>
                    <input type="text"
                           name="header_custom_text"
                           value="{{ old('header_custom_text', $documentDefault->header_custom_text) }}"
                           class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                {{-- Header Logo --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Header Logo Source
                    </label>
                    <select name="header_logo_source"
                            class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="company_logo"
                            {{ $documentDefault->header_logo_source === 'company_logo' ? 'selected' : '' }}>
                            Company Logo
                        </option>
                        <option value="brand_logo"
                            {{ $documentDefault->header_logo_source === 'brand_logo' ? 'selected' : '' }}>
                            Brand Logo
                        </option>
                        <option value="none"
                            {{ $documentDefault->header_logo_source === 'none' ? 'selected' : '' }}>
                            No Logo
                        </option>
                    </select>
                </div>

                {{-- Footer Text --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Footer Text Source
                    </label>
                    <select name="footer_text_source"
                            class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="legal_disclosure"
                            {{ $documentDefault->footer_text_source === 'legal_disclosure' ? 'selected' : '' }}>
                            Legal Disclosure
                        </option>
                        <option value="custom_text"
                            {{ $documentDefault->footer_text_source === 'custom_text' ? 'selected' : '' }}>
                            Custom Text
                        </option>
                        <option value="none"
                            {{ $documentDefault->footer_text_source === 'none' ? 'selected' : '' }}>
                            No Footer
                        </option>
                    </select>
                </div>

                {{-- Custom Footer Text --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Custom Footer Text
                    </label>
                    <textarea name="footer_custom_text"
                              rows="3"
                              class="w-full border-gray-300 rounded-md shadow-sm">{{ old('footer_custom_text', $documentDefault->footer_custom_text) }}</textarea>
                </div>

                {{-- Toggles --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="show_address" value="1"
                            {{ $documentDefault->show_address ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Show Address</span>
                    </label>

                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="show_company_number" value="1"
                            {{ $documentDefault->show_company_number ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Show Company Number</span>
                    </label>

                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="show_vat_number" value="1"
                            {{ $documentDefault->show_vat_number ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Show VAT Number</span>
                    </label>

                    <label class="flex items-center space-x-2">
                        <input type="checkbox" name="show_bank_details" value="1"
                            {{ $documentDefault->show_bank_details ? 'checked' : '' }}>
                        <span class="text-sm text-gray-700">Show Bank Details</span>
                    </label>

                </div>

            </div>

            <div class="mt-6 flex justify-end">
                <button type="submit"
                        class="bg-indigo-600 text-white px-6 py-2 rounded-md hover:bg-indigo-700">
                    Save Defaults
                </button>
            </div>

        </form>

    </div>

</div>

@endsection

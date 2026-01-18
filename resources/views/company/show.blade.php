@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-6">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-2xl font-semibold text-gray-800">
            {{ $company->legal_name }}
        </h1>

        <a href="{{ route('company.edit', $company) }}"
           class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
            Edit Company
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

        {{-- Company Details --}}
        <div class="bg-white shadow rounded p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                Company Details
            </h2>

            <dl class="space-y-2 text-sm">
                <div>
                    <dt class="font-medium text-gray-600">Company Number</dt>
                    <dd>{{ $company->company_number ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-600">VAT Number</dt>
                    <dd>{{ $company->vat_number ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-600">Email</dt>
                    <dd>{{ $company->email ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-600">Phone</dt>
                    <dd>{{ $company->phone ?? '—' }}</dd>
                </div>

                <div>
                    <dt class="font-medium text-gray-600">Status</dt>
                    <dd>
                        <span class="px-2 py-1 rounded text-xs
                            {{ $company->status === 'active'
                                ? 'bg-green-100 text-green-700'
                                : 'bg-gray-200 text-gray-600' }}">
                            {{ ucfirst($company->status) }}
                        </span>
                    </dd>
                </div>
            </dl>
        </div>

        {{-- Address --}}
        <div class="bg-white shadow rounded p-6">
            <h2 class="text-lg font-semibold text-gray-700 mb-4">
                Address
            </h2>

            <p class="text-sm text-gray-700 leading-relaxed">
                {{ $company->address_line1 }}<br>
                @if($company->address_line2)
                    {{ $company->address_line2 }}<br>
                @endif
                @if($company->address_line3)
                    {{ $company->address_line3 }}<br>
                @endif
                {{ $company->city }}<br>
                {{ $company->state_region }}<br>
                {{ $company->postcode }}<br>
                {{ $company->country }}
            </p>
        </div>

        {{-- Brands --}}
        <div class="bg-white shadow rounded p-6 md:col-span-2">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-lg font-semibold text-gray-700">
                    Brands
                </h2>

                <a href="{{ route('brands.index', $company) }}"
                   class="text-indigo-600 hover:underline text-sm">
                    Manage Brands
                </a>
            </div>

            @if ($company->brands->count())
                <ul class="divide-y text-sm">
                    @foreach ($company->brands as $brand)
                        <li class="py-2 flex items-center justify-between">
                            <span>{{ $brand->brand_name }}</span>

                            @if ($brand->is_default_brand)
                                <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-1 rounded">
                                    Default
                                </span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-gray-500">
                    No brands added yet.
                </p>
            @endif
        </div>

        {{-- Document Defaults --}}
        <div class="bg-white shadow rounded p-6 md:col-span-2">
            <h2 class="text-lg font-semibold text-gray-700 mb-2">
                Document Defaults
            </h2>

            <a href="{{ route('document-defaults.edit', $company) }}"
               class="text-indigo-600 hover:underline text-sm">
                Edit Document Defaults
            </a>
        </div>

    </div>

</div>
@endsection

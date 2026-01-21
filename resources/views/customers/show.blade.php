@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto py-6 space-y-8">

    {{-- =====================================================
    HEADER
    ====================================================== --}}
    <div class="bg-white shadow rounded px-6 py-5">
        <div class="flex items-center justify-between">
            <div>
                <div class="text-sm font-medium text-gray-500">
                    Customer Account
                </div>

                <div class="text-3xl font-bold font-mono text-gray-900 leading-tight">
                    {{ $customer->account_number }}
                </div>
            </div>

            <span class="inline-flex items-center px-4 py-1.5 text-sm rounded-full font-semibold
                @if ($customer->customer_status === 'active') bg-green-100 text-green-800
                @elseif($customer->customer_status === 'on_hold') bg-yellow-100 text-yellow-800
                @else bg-red-100 text-red-800 @endif">
                {{ str_replace('_', ' ', $customer->customer_status) }}
            </span>
        </div>

        <div class="mt-4 border-t"></div>

        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-1">
                <div class="text-lg font-semibold text-gray-900">
                    {{ $customer->contact_name }}
                </div>

                @if ($customer->registered_company_name)
                    <div class="text-sm text-gray-600">
                        {{ $customer->registered_company_name }}
                    </div>
                @endif
            </div>

            <div class="space-y-1 text-sm text-gray-700">
                @if ($customer->customer_registration_number)
                    <div>
                        <span class="font-medium text-gray-500">Reg No:</span>
                        {{ $customer->customer_registration_number }}
                    </div>
                @endif

                @if ($customer->vat_number)
                    <div>
                        <span class="font-medium text-gray-500">VAT:</span>
                        {{ $customer->vat_number }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="mt-2 text-xs text-gray-500">
        Managed under company:
        <span class="font-medium text-gray-700">
            {{ $customer->company->legal_name }}
        </span>
    </div>

    {{-- =====================================================
    CUSTOMER DETAILS
    ====================================================== --}}
    <div class="bg-white shadow rounded">
        <div class="border-b p-4 flex justify-between items-center">
            <h2 class="font-semibold">Customer Details</h2>

            {{-- If this link "doesn't work", it's almost always route or auth.
                 We'll give you a quick route-list command below. --}}
            <a href="{{ route('customers.edit', $customer) }}" class="text-blue-600 text-sm hover:underline">
                Edit Customer
            </a>
        </div>

        <div class="p-6 text-sm">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-3">
                    <div><span class="text-gray-500">Email:</span> {{ $customer->email ?? '—' }}</div>
                    <div><span class="text-gray-500">Primary Phone:</span> {{ $customer->primary_phone ?? '—' }}</div>
                    <div><span class="text-gray-500">Secondary Phone:</span> {{ $customer->secondary_phone ?? '—' }}</div>
                </div>

                <div class="space-y-3">
                    <div><span class="text-gray-500">VAT:</span> {{ $customer->vat_number ?? '—' }}</div>
                    <div>
                        <span class="text-gray-500">Credit Limit:</span>
                        {{ $customer->credit_limit !== null ? number_format($customer->credit_limit, 2) : '—' }}
                    </div>
                    <div>
                        <span class="text-gray-500">Payment Terms:</span>
                        {{ str_replace('_', ' ', $customer->payment_terms ?? '—') }}
                    </div>
                </div>
            </div>

            @if ($customer->internal_notes)
                <div class="mt-6 pt-6 border-t">
                    <div class="text-gray-500 mb-1">Internal Notes</div>
                    <div class="bg-gray-50 border rounded p-3 whitespace-pre-line">
                        {{ $customer->internal_notes }}
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- =====================================================
    ADDRESSES (MODAL-BASED)
    ====================================================== --}}
    @include('customers._addresses')

    {{-- =====================================================
    PRODUCT PORTFOLIO (keep your existing partial)
    ====================================================== --}}
    @php
        $products = $products ?? \App\Models\Product\Product::with('variations')->orderBy('product_name')->get();
        $portfolio = $portfolio ?? collect();
        $activePortfolioMap = $activePortfolioMap ?? collect();
    @endphp

    @include('customers._portfolio')

    {{-- =====================================================
    FINANCIAL SUMMARY (PLACEHOLDER)
    ====================================================== --}}
    <div class="bg-white shadow rounded p-6">
        <h2 class="font-semibold mb-1">Financial Summary</h2>
        <p class="text-sm text-gray-500">
            Invoices, outstanding balance, overdue amounts, statements.
        </p>
    </div>

    {{-- =====================================================
    COMMUNICATIONS (PLACEHOLDER)
    ====================================================== --}}
    <div class="bg-white shadow rounded p-6">
        <h2 class="font-semibold mb-1">Communications</h2>
        <p class="text-sm text-gray-500">
            Messages, notes, emails/calls, and customer-visible communications.
        </p>
    </div>

</div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 space-y-8">

        {{-- =====================================================
        HEADER
        ====================================================== --}}
        <div class="bg-white shadow rounded px-6 py-5">

            {{-- Row 1: Account + Status --}}
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium text-gray-500">
                        Customer Account
                    </div>

                    {{-- DO NOT TOUCH THIS --}}
                    <div class="text-3xl font-bold font-mono text-gray-900 leading-tight">
                        {{ $customer->account_number }}
                    </div>
                </div>

                <span
                    class="inline-flex items-center px-4 py-1.5 text-sm rounded-full font-semibold
            @if ($customer->customer_status === 'active') bg-green-100 text-green-800
            @elseif($customer->customer_status === 'on_hold') bg-yellow-100 text-yellow-800
            @else bg-red-100 text-red-800 @endif">
                    {{ str_replace('_', ' ', $customer->customer_status) }}
                </span>
            </div>

            {{-- Divider --}}
            <div class="mt-4 border-t"></div>

            {{-- Row 2: Identity & Legal --}}
            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">

                {{-- Left: Human + Company --}}
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

                {{-- Right: Legal Identifiers --}}
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
                <a href="{{ route('customers.edit', $customer) }}" class="text-blue-600 text-sm hover:underline">
                    Edit Customer
                </a>
            </div>

            <div class="p-6 text-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div><span class="text-gray-500">Email:</span> {{ $customer->email ?? '—' }}</div>
                        <div><span class="text-gray-500">Primary Phone:</span> {{ $customer->primary_phone ?? '—' }}</div>
                        <div><span class="text-gray-500">Secondary Phone:</span> {{ $customer->secondary_phone ?? '—' }}
                        </div>
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
        ADDRESSES (as-is, inline)
        ====================================================== --}}
        @php
            $registered = $customer->addresses->where('address_type', 'registered')->where('is_active', true)->first();
            $billing = $customer->addresses->where('address_type', 'billing')->where('is_active', true)->first();
            $deliveryActive = $customer->addresses->where('address_type', 'delivery')->where('is_active', true);
            $defaultDelivery = $deliveryActive->firstWhere('is_default', true);

            $renderAddress = function ($address) {
                return implode(
                    '<br>',
                    array_filter([
                        $address->address_line1,
                        $address->address_line2,
                        $address->address_line3,
                        trim($address->city . ($address->state_region ? ', ' . $address->state_region : '')),
                        $address->postcode,
                        $address->country,
                    ]),
                );
            };
        @endphp

        <div class="bg-white shadow rounded">
            <div class="border-b p-4">
                <h2 class="font-semibold">Addresses</h2>
            </div>

            <div class="p-4 space-y-6 text-sm">

                {{-- Registered --}}
                <div>
                    <div class="flex justify-between mb-1">
                        <h3 class="font-medium">Registered Address *</h3>
                        <a href="{{ route('customers.addresses.create', ['customer' => $customer, 'type' => 'registered']) }}"
                            class="text-blue-600 text-sm hover:underline">
                            {{ $registered ? 'Replace' : 'Add' }}
                        </a>
                    </div>

                    @if ($registered)
                        <div class="border rounded p-3 bg-gray-50">
                            {!! $renderAddress($registered) !!}
                        </div>
                    @else
                        <div class="text-yellow-700 bg-yellow-50 border border-yellow-200 p-3 rounded">
                            Registered address is required.
                        </div>
                    @endif
                </div>

                {{-- Billing --}}
                <div>
                    <div class="flex justify-between mb-1">
                        <h3 class="font-medium">Billing Address *</h3>
                        <a href="{{ route('customers.addresses.create', ['customer' => $customer, 'type' => 'billing']) }}"
                            class="text-blue-600 text-sm hover:underline">
                            {{ $billing ? 'Replace' : 'Add' }}
                        </a>
                    </div>

                    @if ($billing)
                        <div class="border rounded p-3 bg-gray-50">
                            {!! $renderAddress($billing) !!}
                        </div>
                    @else
                        <div class="text-yellow-700 bg-yellow-50 border border-yellow-200 p-3 rounded">
                            Billing address is required.
                        </div>
                    @endif
                </div>

                {{-- Delivery --}}
                <div>
                    <div class="flex justify-between mb-1">
                        <h3 class="font-medium">Default Delivery Address *</h3>
                        <a href="{{ route('customers.addresses.create', ['customer' => $customer, 'type' => 'delivery']) }}"
                            class="text-blue-600 text-sm hover:underline">
                            Add Delivery
                        </a>
                    </div>

                    @if ($defaultDelivery)
                        <div class="border rounded p-3 bg-gray-50 flex items-start gap-4">
                            <div class="flex-1">{!! $renderAddress($defaultDelivery) !!}</div>
                            <span class="inline-block text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                Default
                            </span>
                        </div>
                    @else
                        <div class="text-yellow-700 bg-yellow-50 border border-yellow-200 p-3 rounded">
                            At least one delivery address is required.
                        </div>
                    @endif

                    @if ($deliveryActive->count() > 1)
                        <details class="mt-2">
                            <summary class="cursor-pointer text-blue-600 text-sm">
                                View all delivery addresses ({{ $deliveryActive->count() }})
                            </summary>

                            <div class="mt-3 space-y-2">
                                @foreach ($deliveryActive as $delivery)
                                    <div class="border rounded p-3 flex items-start gap-4">
                                        <div class="flex-1">{!! $renderAddress($delivery) !!}</div>
                                        <div class="flex gap-2 text-xs">
                                            <a href="{{ route('customers.addresses.edit', $delivery) }}"
                                                class="text-blue-600 hover:underline">Edit</a>
                                            <form method="POST"
                                                action="{{ route('customers.addresses.deactivate', $delivery) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-red-600">Deactivate</button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </details>
                    @endif
                </div>

            </div>
        </div>

        {{-- =====================================================
        PRODUCT PORTFOLIO (Add via Modal + List)
        ====================================================== --}}

        @php
            // SAFETY: ensure these exist so the page never crashes.
            $products =
                $products ?? \App\Models\Product\Product::query()->with('variations')->orderBy('product_name')->get();

            $portfolio = $portfolio ?? collect();

            $activePortfolioMap = $activePortfolioMap ?? collect();
        @endphp

        <div x-data="{ open: false }">
            <div class="bg-white shadow rounded">
                <div class="border-b p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold">Product Portfolio</h2>

                        <button type="button" @click="open = true"
                            class="px-4 py-2 text-sm bg-blue-600 text-white rounded hover:bg-blue-700">
                            Add Product
                        </button>
                    </div>
                </div>

                <div class="p-4 text-sm space-y-4">

                    {{-- Portfolio List --}}
                    @if ($portfolio->isEmpty())
                        <div class="bg-gray-50 border rounded p-4 text-gray-600">
                            No products assigned to this customer.
                        </div>
                    @else
                        <table class="w-full border text-sm">
                            <thead class="bg-gray-50 border-b">
                                <tr>
                                    <th class="p-2 text-left">Product</th>
                                    <th class="p-2 text-left">Variant</th>
                                    <th class="p-2 text-right">Customer Price</th>
                                    <th class="p-2 text-right">Standard Price</th>
                                    <th class="p-2 text-center">Status</th>
                                    <th class="p-2 text-right">Actions</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse ($portfolio as $entry)
                                    <tr class="border-t">
                                        <td class="p-2">
                                            {{ $entry->product->product_name ?? '—' }}
                                        </td>

                                        <td class="p-2 text-sm text-gray-600">
                                            @if ($entry->variation)
                                                {{ $entry->variation->length }} ×
                                                {{ $entry->variation->width }} ×
                                                {{ $entry->variation->thickness }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        {{-- Customer Price (inline edit) --}}
                                        <td class="p-2 text-right font-medium" x-data="{ editing: false }">
                                            <div x-show="!editing">
                                                {{ number_format($entry->agreed_price, 2) }}

                                                @if ($entry->is_active)
                                                    <button type="button"
                                                        class="ml-2 text-xs text-blue-600 hover:underline"
                                                        @click="editing = true">
                                                        Edit
                                                    </button>
                                                @endif
                                            </div>

                                            <div x-show="editing" x-cloak>
                                                <form method="POST"
                                                    action="{{ route('customers.portfolio.update', $entry) }}"
                                                    class="flex items-center justify-end gap-2">
                                                    @csrf
                                                    @method('PUT')

                                                    <input type="number" name="agreed_price" step="0.01"
                                                        min="0" value="{{ $entry->agreed_price }}"
                                                        class="w-24 border rounded px-2 py-1 text-sm" required>

                                                    <button type="submit" class="text-xs text-green-600 hover:underline">
                                                        Save
                                                    </button>

                                                    <button type="button" class="text-xs text-gray-500 hover:underline"
                                                        @click="editing = false">
                                                        Cancel
                                                    </button>
                                                </form>
                                            </div>
                                        </td>

                                        {{-- Standard Price --}}
                                        <td class="p-2 text-right text-gray-500">
                                            @if (($entry->product->product_type ?? null) === 'simple')
                                                {{ number_format($entry->product->simple_price, 2) }}
                                            @elseif (($entry->product->product_type ?? null) === 'variant_based' && $entry->variation)
                                                {{ number_format($entry->variation->standard_price, 2) }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td class="p-2 text-center">
                                            @if ($entry->is_active)
                                                <span class="bg-green-100 text-green-800 text-xs px-2 py-1 rounded">
                                                    Active
                                                </span>
                                            @else
                                                <span class="bg-gray-200 text-gray-700 text-xs px-2 py-1 rounded">
                                                    Inactive
                                                </span>
                                            @endif
                                        </td>

                                        <td class="p-2 text-right">
                                            @if ($entry->is_active)
                                                <form method="POST"
                                                    action="{{ route('customers.portfolio.deactivate', $entry) }}">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button class="text-red-600 text-sm">Deactivate</button>
                                                </form>
                                            @else
                                                —
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-4 text-center text-gray-500">
                                            No products in portfolio yet.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    @endif

                </div>
            </div>

            {{-- Modal (x-if resets modal state on reopen) --}}
            <template x-if="open">
                @include('customers.portfolio.add-modal', [
                    'products' => $products,
                    'activePortfolioMap' => $activePortfolioMap ?? [],
                ])
            </template>
        </div>

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

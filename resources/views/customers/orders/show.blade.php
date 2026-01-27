@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- ================= DOCUMENT HEADER ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>
                    <div class="text-xs uppercase tracking-wider text-gray-500 mb-1">
                        Order
                    </div>

                    <h1 class="text-3xl font-semibold text-gray-900">
                        {{ $order->order_number }}
                    </h1>
                </div>

                <div>
                    @php
                        $statusClasses = [
                            'draft' => 'bg-gray-100 text-gray-800',
                            'submitted' => 'bg-blue-100 text-blue-800',
                            'acknowledged' => 'bg-green-100 text-green-800',
                            'cancelled' => 'bg-red-100 text-red-800',
                        ];
                    @endphp

                    <span
                        class="inline-flex items-center rounded-full px-4 py-1.5 text-sm font-medium
                    {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                        {{ ucfirst($order->status) }}
                    </span>
                </div>

            </div>
        </div>

        {{-- ================= ACTION BAR (DRAFT ONLY) ================= --}}
        @if ($order->status === 'draft')
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 flex items-center justify-between">

                    <a href="{{ route('orders.add-products', [$customer, $order]) }}" class="crm-btn-secondary">
                        ← Add / Modify Products
                    </a>

                    @if ($order->orderLines->count() > 0)
                        <a href="{{ route('orders.review', [$customer, $order]) }}{{ request('context') === 'staff' ? '?context=staff' : '' }}"
                            class="crm-btn-primary px-8">
                            Proceed to Review &amp; Submit →
                        </a>
                    @endif

                </div>
            </div>
        @endif

        {{-- ================= ORDER BODY ================= --}}
        @if ($order->orderLines->count() === 0)
            {{-- EMPTY STATE --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
                <div class="px-6 py-12 text-center">

                    <h2 class="text-lg font-semibold text-gray-900 mb-2">
                        No products added yet
                    </h2>

                    <p class="text-sm text-gray-500 mb-6">
                        This order does not contain any products.
                    </p>

                    <a href="{{ route('orders.add-products', [$customer, $order]) }}" class="crm-btn-primary px-8">
                        Add Products to Order
                    </a>

                </div>
            </div>
        @else
            @php
                $groupedLines = $order->orderLines->groupBy('product_id');
            @endphp

            {{-- ================= ORDER TABLE ================= --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
                <table class="min-w-full border-collapse">

                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Product / Variation
                            </th>

                            <th
                                class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">
                                Requested Quantity
                            </th>

                            <th
                                class="px-6 py-3 text-center text-xs font-semibold text-gray-600 uppercase tracking-wider w-48 hidden print:table-cell">
                                Processed Quantity
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @foreach ($groupedLines as $lines)
                            @php
                                $product = $lines->first()->product;
                            @endphp

                            {{-- PRODUCT HEADER --}}
                            <tr class="bg-gray-100">
                                <td colspan="3" class="px-6 py-3 font-semibold text-gray-800">
                                    {{ $product->product_name ?? 'Product' }}
                                </td>
                            </tr>

                            {{-- VARIATION ROWS --}}
                            @foreach ($lines as $line)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-8 py-3 text-sm text-gray-700">
                                        {{ $line->productVariation?->display_name ?? '—' }}
                                    </td>

                                    <td class="px-6 py-3 text-right">
                                        @if ($order->status === 'draft')
                                            <input type="number" min="0"
                                                class="w-32 rounded-md border border-gray-300 px-3 py-2 text-sm
                                                   focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                                                value="{{ $line->requested_quantity }}">
                                        @else
                                            <span class="text-sm font-semibold text-gray-900">
                                                {{ $line->requested_quantity }}
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-3 hidden print:table-cell">
                                        <div class="h-8 border-b border-gray-400"></div>
                                    </td>
                                </tr>
                            @endforeach
                        @endforeach
                    </tbody>

                </table>
            </div>
        @endif

        {{-- ================= STAFF ACTION FOOTER ================= --}}
        @if (in_array($order->status, ['submitted', 'acknowledged']))
            <div class="mt-8 flex items-center justify-end gap-4 print:hidden">

                @if (request('context') === 'staff' && $order->status === 'submitted')
                    <form method="POST" action="{{ route('staff.orders.amend', $order) }}"
                        onsubmit="return confirm('This will reopen the order for editing. You must review and re-submit after changes.');">
                        @csrf
                        <button type="submit" class="crm-btn-secondary">
                            ✏️ Amend Order
                        </button>
                    </form>
                @endif
                @if (request('context') === 'staff' && $order->status === 'submitted')
                    <form method="POST" action="{{ route('staff.orders.acknowledge', $order) }}"
                        onsubmit="return confirm('Acknowledge this order for processing?');">
                        @csrf
                        <button type="submit" class="crm-btn-primary">
                            ✅ Acknowledge Order
                        </button>
                    </form>
                @endif

                @if (request('context') === 'staff' && $order->status === 'acknowledged')
                    <a href="{{ route('staff.delivery-notes.create', $order) }}" class="crm-btn-primary">
                        🚚 Create Delivery / Collection Note
                    </a>
                @endif

                <a href="{{ route('orders.print', [$customer, $order]) }}" target="_blank" class="crm-btn-secondary">
                    🖨️ Print Order Sheet
                </a>


            </div>
        @endif

    </div>

    <style>
        @media print {
            td {
                padding-top: 14px !important;
                padding-bottom: 14px !important;
            }
        }
    </style>
@endsection

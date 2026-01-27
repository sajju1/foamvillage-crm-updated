@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- ================= HEADER ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="px-6 py-5 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

                <div>
                    <div class="text-xs uppercase tracking-wider text-gray-500 mb-1">
                        Review Order
                    </div>

                    <h1 class="text-2xl font-semibold text-gray-900">
                        {{ $order->order_number }}
                    </h1>
                </div>

                <div>
                    <span
                        class="inline-flex items-center rounded-full
                             bg-yellow-100 text-yellow-800
                             px-4 py-1.5 text-sm font-medium">
                        Pending Submission
                    </span>
                </div>

            </div>
        </div>

        {{-- ================= INFO NOTE ================= --}}
        <div class="mb-6 rounded-md border border-yellow-200 bg-yellow-50 px-5 py-4 text-sm text-yellow-900">
            <strong class="font-semibold">Please review carefully.</strong>
            <span class="ml-1">
                Once submitted, this order cannot be edited
                @if(request('context') === 'staff')
                    without amending it again.
                @else
                    by the customer.
                @endif
            </span>
        </div>

        @php
            $groupedLines = $order->orderLines->groupBy('product_id');
        @endphp

        {{-- ================= ORDER SUMMARY TABLE ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto mb-10">
            <table class="min-w-full border-collapse">

                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Product / Variation
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider w-48">
                            Requested Quantity
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @foreach ($groupedLines as $productId => $lines)
                        @php
                            $product = $lines->first()->product;
                        @endphp

                        {{-- PRODUCT HEADER --}}
                        <tr class="bg-gray-100">
                            <td colspan="2" class="px-6 py-3 font-semibold text-gray-800">
                                {{ $product->product_name ?? 'Product' }}
                            </td>
                        </tr>

                        {{-- VARIATION ROWS --}}
                        @foreach ($lines as $line)
                            <tr class="hover:bg-gray-50">
                                <td class="px-8 py-2 text-sm text-gray-700">
                                    {{ $line->productVariation?->display_name ?? '—' }}
                                </td>

                                <td class="px-6 py-2 text-sm font-semibold text-gray-900 text-right">
                                    {{ $line->requested_quantity }}
                                </td>
                            </tr>
                        @endforeach
                    @endforeach
                </tbody>

            </table>
        </div>

        {{-- ================= ACTION BAR ================= --}}
        <div class="flex items-center justify-between border-t border-gray-200 pt-6">

            {{-- Back --}}
            <a href="{{ route('orders.show', [$customer, $order]) }}{{ request('context') === 'staff' ? '?context=staff' : '' }}"
               class="crm-btn-secondary">
                ← Back to Order
            </a>

            {{-- Submit --}}
            <form method="POST"
                  action="{{ route('orders.submit', [$customer, $order]) }}{{ request('context') === 'staff' ? '?context=staff' : '' }}">
                @csrf
                <button type="submit" class="crm-btn-primary px-8">
                    Submit Order
                </button>
            </form>

        </div>

    </div>
@endsection

@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- ================= HEADER ================= --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">
                Create Delivery / Collection Note
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Order {{ $order->order_number }}
            </p>
        </div>

        {{-- ================= CUSTOMER DETAILS ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">

                <div>
                    <div class="text-gray-500">Customer</div>
                    <div class="font-semibold text-gray-900">
                        {{ $customer->customer_name ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Account Number</div>
                    <div class="font-semibold text-gray-900">
                        {{ $customer->account_number ?? '—' }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Order Number</div>
                    <div class="font-semibold text-gray-900">
                        {{ $order->order_number }}
                    </div>
                </div>

            </div>
        </div>

        <form method="POST" action="{{ route('staff.delivery-notes.store', $order) }}">
            @csrf

            {{-- ================= TYPE & ADDRESS ================= --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-6">

                    {{-- Type --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Note Type
                        </label>
                        <select name="type" class="w-full rounded-md border-gray-300">
                            <option value="delivery">Delivery</option>
                            <option value="collection">Collection</option>
                        </select>
                    </div>

                    {{-- Address --}}
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Delivery Address
                        </label>

                        <select name="delivery_address_id" class="w-full rounded-md border-gray-300">
                            @foreach ($addresses as $address)
                                <option value="{{ $address->id }}" @if (optional($defaultAddress)->id === $address->id) selected @endif>
                                    {{ $address->address_line1 }}
                                    @if (!empty($address->address_line2))
                                        , {{ $address->address_line2 }}
                                    @endif
                                    @if (!empty($address->address_line3))
                                        , {{ $address->address_line3 }}
                                    @endif
                                    , {{ $address->city }}
                                    @if (!empty($address->postcode))
                                        , {{ $address->postcode }}
                                    @endif
                                    ({{ ucfirst($address->address_type) }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                </div>
            </div>

            {{-- ================= ORDER LINES ================= --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6 overflow-x-auto">
                <table class="min-w-full border-collapse">

                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                                Product / Variation
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                Requested
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                                Processed
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-200">
                        @foreach ($order->orderLines as $line)
                            <tr>
                                <td class="px-6 py-3 text-sm text-gray-700">
                                    {{ $line->product->product_name ?? 'Product' }}
                                    <div class="text-xs text-gray-500">
                                        {{ $line->productVariation?->display_name ?? '—' }}
                                    </div>
                                </td>

                                <td class="px-6 py-3 text-right text-sm font-semibold">
                                    {{ $line->requested_quantity }}
                                </td>

                                <td class="px-6 py-3 text-right">
                                    <input type="number" min="0" step="1"
                                        name="lines[{{ $loop->index }}][processed_quantity]"
                                        class="w-24 rounded-md border-gray-300 text-sm"
                                        value="{{ $line->requested_quantity }}">


                                    <input type="hidden" name="lines[{{ $loop->index }}][order_line_id]"
                                        value="{{ $line->id }}">
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                </table>
            </div>

            {{-- ================= ACTIONS ================= --}}
            <div class="flex items-center justify-end gap-4">
                <a href="{{ route('staff.orders.show', $order) }}" class="crm-btn-secondary">
                    Cancel
                </a>

                <button type="submit" class="crm-btn-primary px-8">
                    Create Delivery Note
                </button>
            </div>

        </form>

    </div>
@endsection

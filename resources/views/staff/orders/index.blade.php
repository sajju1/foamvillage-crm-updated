@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- ================= PAGE HEADER ================= --}}
        <div class="mb-8 flex flex-col md:flex-row md:items-center md:justify-between gap-4">

            <div>
                <div class="text-xs uppercase tracking-wider text-gray-500 mb-2">
                    Staff
                </div>

                <h1 class="text-2xl font-semibold text-gray-900">
                    Orders
                </h1>

                <p class="text-sm text-gray-500 mt-1">
                    Review, acknowledge, and manage all customer orders.
                </p>
            </div>

            <a href="{{ route('staff.orders.create') }}" class="crm-btn-primary px-8">
                + Create Order
            </a>

        </div>

        {{-- ================= ORDERS TABLE ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full border-collapse">

                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Order #
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Customer Account
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Status
                        </th>

                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Created
                        </th>

                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Action
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">

                    @forelse ($orders as $order)
                        @php
                            $statusClasses = [
                                'draft' => 'bg-gray-100 text-gray-800',
                                'submitted' => 'bg-yellow-100 text-yellow-800',
                                'acknowledged' => 'bg-green-100 text-green-800',
                                'cancelled' => 'bg-red-100 text-red-800',
                            ];
                        @endphp

                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-3 font-medium text-gray-900">
                                {{ $order->order_number }}
                            </td>

                            <td class="px-6 py-3 text-sm text-gray-700">
                                {{ $order->customer->account_number ?? '—' }}
                            </td>

                            <td class="px-6 py-3">
                                <span
                                    class="inline-flex items-center rounded-full px-3 py-1 text-sm font-medium
                                    {{ $statusClasses[$order->status] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($order->status) }}
                                </span>
                            </td>

                            <td class="px-6 py-3 text-sm text-gray-600">
                                {{ $order->created_at->format('d M Y') }}
                            </td>

                            <td class="px-6 py-3 text-right space-x-2">

                                @if ($order->status === 'submitted')
                                    <a href="{{ route('orders.show', [$order->customer, $order]) }}?context=staff"
                                       class="crm-btn-primary text-sm px-4">
                                        Review
                                    </a>

                                @elseif ($order->status === 'draft')
                                    <a href="{{ route('orders.show', [$order->customer, $order]) }}?context=staff"
                                       class="crm-btn-secondary text-sm px-4">
                                        Continue
                                    </a>

                                @else
                                    <a href="{{ route('orders.show', [$order->customer, $order]) }}?context=staff"
                                       class="crm-btn-secondary text-sm px-4">
                                        View
                                    </a>
                                @endif

                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-10 text-center text-sm text-gray-500">
                                No orders found.
                            </td>
                        </tr>
                    @endforelse

                </tbody>

            </table>
        </div>

    </div>
@endsection

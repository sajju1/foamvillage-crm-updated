@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-6">

    <div class="flex items-center justify-between mb-6">
        <h1 class="text-xl font-semibold">Orders</h1>
    </div>

    @if(session('success'))
        <div class="mb-4 text-green-700">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Order No</th>
                    <th class="px-4 py-3 text-left">Customer</th>
                    <th class="px-4 py-3 text-left">Status</th>
                    <th class="px-4 py-3 text-left">Created</th>
                    <th class="px-4 py-3 text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr class="border-t">
                        <td class="px-4 py-3 font-medium">
                            {{ $order->order_number }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $order->customer->contact_name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 capitalize">
                            {{ str_replace('_', ' ', $order->status) }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $order->created_at?->format('d M Y') }}
                        </td>
                        <td class="px-4 py-3 text-right space-x-3">
                            <a href="{{ route('orders.show', $order) }}" class="underline">
                                View
                            </a>

                            @if($order->isEditable())
                                <a href="{{ route('orders.edit', $order) }}" class="underline">
                                    Edit
                                </a>
                            @endif

                            <a href="{{ route('orders.print', $order) }}" target="_blank" class="underline">
                                Print
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            No orders found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $orders->links() }}
    </div>

</div>
@endsection

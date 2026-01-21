@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-6">

    <div class="flex items-center justify-between mb-6">
        <div>
            <h1 class="text-xl font-semibold">Order Sheet</h1>
            <div class="text-sm text-gray-600">
                Order No: <strong>{{ $order->order_number }}</strong>
            </div>
        </div>

        <div class="space-x-3">
            @if($order->isEditable())
                <a href="{{ route('orders.edit', $order) }}" class="underline">
                    Edit
                </a>
            @endif

            <a href="{{ route('orders.print', $order) }}" target="_blank" class="underline">
                Print
            </a>
        </div>
    </div>

    {{-- Order Meta --}}
    <div class="bg-white shadow rounded p-4 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
            <div>
                <div class="text-gray-500">Customer</div>
                <div class="font-medium">
                    {{ $order->customer->contact_name ?? '—' }}
                </div>
            </div>

            <div>
                <div class="text-gray-500">Status</div>
                <div class="capitalize font-medium">
                    {{ str_replace('_', ' ', $order->status) }}
                </div>
            </div>

            <div>
                <div class="text-gray-500">Created</div>
                <div class="font-medium">
                    {{ $order->created_at?->format('d M Y H:i') }}
                </div>
            </div>
        </div>
    </div>

    {{-- Order Lines --}}
    <div class="bg-white shadow rounded overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-gray-100">
                <tr>
                    <th class="px-4 py-3 text-left">Product</th>
                    <th class="px-4 py-3 text-left">Variation</th>
                    <th class="px-4 py-3 text-right">Requested</th>
                    <th class="px-4 py-3 text-right">Processed</th>
                    <th class="px-4 py-3 text-right">Pending</th>
                </tr>
            </thead>
            <tbody>
                @forelse($order->lines->where('line_status', 'active') as $line)
                    <tr class="border-t">
                        <td class="px-4 py-3">
                            {{ $line->product->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            {{ $line->variation->name ?? '—' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            {{ $line->requested_quantity }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            {{ $line->processed_quantity }}
                        </td>
                        <td class="px-4 py-3 text-right font-medium">
                            {{ $line->pendingQuantity() }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                            No order lines added.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Actions --}}
    @if($order->isDraft())
        <div class="mt-6 flex justify-end">
            <form method="POST" action="{{ route('orders.submit', $order) }}">
                @csrf
                <button class="bg-gray-900 text-white px-4 py-2 rounded">
                    Submit Order
                </button>
            </form>
        </div>
    @endif

</div>
@endsection

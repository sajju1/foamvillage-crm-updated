@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-6">

        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-xl font-semibold">Edit Order Sheet</h1>
                <div class="text-sm text-gray-600">
                    Order No: <strong>{{ $order->order_number }}</strong><br>
                    Customer: <strong>{{ $order->customer->contact_name }}</strong>
                    ({{ $order->customer->account_number }})
                </div>
            </div>

            <div class="space-x-3">
                <a href="{{ route('orders.show', $order) }}" class="underline">View</a>
                <a href="{{ route('orders.print', $order) }}" target="_blank" class="underline">Print</a>
            </div>
        </div>

        {{-- Messages --}}
        @if (session('success'))
            <div class="mb-4 text-green-700">
                {{ session('success') }}
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-4 text-red-600">
                {{ $errors->first() }}
            </div>
        @endif

        {{-- Add product from portfolio --}}
        <div class="bg-white shadow rounded p-4 mb-6">
            <h2 class="font-semibold mb-4">Add Product (Customer Portfolio)</h2>

            <form method="POST" action="{{ route('orders.lines.add', $order) }}"
                class="grid grid-cols-1 md:grid-cols-4 gap-4">
                @csrf

                <div class="md:col-span-2">
                    <label class="block text-sm mb-1">Product</label>
                    <select name="portfolio_item_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select product</option>
                        @foreach ($portfolioItems as $item)
                            <option value="{{ $item->id }}">
                                {{ $item->display_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm mb-1">Quantity</label>
                    <input type="number" name="requested_quantity" min="1" class="w-full border rounded px-3 py-2"
                        required>
                </div>

                <div class="flex items-end">
                    <button class="bg-gray-900 text-white px-4 py-2 rounded w-full">
                        Add Line
                    </button>
                </div>
            </form>
        </div>

        {{-- Order lines --}}
        <div class="bg-white shadow rounded overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Product</th>
                        <th class="px-4 py-3 text-left">Variation</th>
                        <th class="px-4 py-3 text-right">Requested</th>
                        <th class="px-4 py-3 text-right">Processed</th>
                        <th class="px-4 py-3 text-right">Pending</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($order->lines->where('line_status','active') as $line)
                        <tr class="border-t">
                            <td class="px-4 py-3">
                                {{ $line->product->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3">
                                {{ $line->variation->name ?? '—' }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('orders.lines.update', [$order, $line]) }}"
                                    class="inline-flex gap-2">
                                    @csrf
                                    @method('PUT')
                                    <input type="number" name="requested_quantity" value="{{ $line->requested_quantity }}"
                                        min="1" class="w-20 border rounded px-2 py-1 text-right">
                                    <button class="underline text-sm">Save</button>
                                </form>
                            </td>
                            <td class="px-4 py-3 text-right">
                                {{ $line->processed_quantity }}
                            </td>
                            <td class="px-4 py-3 text-right font-medium">
                                {{ $line->pendingQuantity() }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                <form method="POST" action="{{ route('orders.lines.cancel', [$order, $line]) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 underline text-sm">
                                        Remove
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                No products added yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Submit --}}
        @if ($order->isDraft())
            <div class="mt-6 flex justify-end">
                <form method="POST" action="{{ route('orders.submit', $order) }}">
                    @csrf
                    <button class="bg-gray-900 text-white px-6 py-2 rounded">
                        Submit Order
                    </button>
                </form>
            </div>
        @endif

    </div>
@endsection

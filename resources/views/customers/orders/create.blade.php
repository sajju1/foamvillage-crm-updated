@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">
            Create Order
        </h1>
        <p class="text-sm text-gray-500 mt-1">
            Select products from the customer portfolio and enter requested quantities.
        </p>
    </div>

    <form method="POST" action="#">
        @csrf

        {{-- Order Lines Table --}}
        <div class="overflow-x-auto bg-white border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full border-collapse">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Product
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                            Requested Qty
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @php
                        $groupedPortfolio = $portfolio->groupBy('product_id');
                    @endphp

                    @foreach ($groupedPortfolio as $productId => $items)
                        @php
                            $product = $items->first()->product;
                        @endphp

                        {{-- Product group header --}}
                        <tr class="bg-gray-100">
                            <td colspan="2" class="px-6 py-3">
                                <span class="font-semibold text-gray-800">
                                    {{ $product->product_name }}
                                </span>
                            </td>
                        </tr>

                        {{-- Variations / line rows --}}
                        @foreach ($items as $item)
                            @include('customers.orders.partials.line-row', [
                                'item' => $item,
                                'order' => $order,
                            ])
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Action Bar --}}
        <div class="mt-6 flex justify-end">
            <a
                href="{{ route('orders.review', [$customer, $order]) }}"
                class="crm-btn-primary"
            >
                Review Order
            </a>
        </div>
    </form>
</div>
@endsection

{{-- Quantity change handler --}}
<script>
    document.querySelectorAll('.js-qty').forEach(input => {
        input.addEventListener('change', function () {

            const url =
                '{{ route('customers.orders.lines.upsert', [$customer->id, $order->id]) }}';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                body: JSON.stringify({
                    product_id: this.dataset.productId,
                    product_variation_id: this.dataset.variationId,
                    requested_quantity: this.value
                })
            })
            .catch(error => {
                console.error('Order line save failed:', error);
            });

        });
    });
</script>

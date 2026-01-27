@php
    $lines = $order->orderLines ?? collect();

    $line = $lines->first(function ($line) use ($item) {
        return $line->product_id == $item->product_id
            && $line->product_variation_id == $item->product_variation_id;
    });
@endphp

<tr class="hover:bg-gray-50">
    {{-- Variation / Product Name --}}
    <td class="px-6 py-3 text-sm text-gray-700">
        @if ($item->productVariation)
            {{ $item->productVariation->name }}
        @else
            <span class="italic text-gray-400">Standard</span>
        @endif
    </td>

    {{-- Requested Quantity --}}
    <td class="px-6 py-3">
        <input
            type="number"
            min="0"
            class="js-qty w-28 rounded-md border border-gray-300 px-3 py-2 text-sm
                   focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
            data-product-id="{{ $item->product_id }}"
            data-variation-id="{{ $item->product_variation_id }}"
            value="{{ $line->requested_quantity ?? '' }}"
        >
    </td>
</tr>

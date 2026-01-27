@php
    $productName = $line->product->product_name ?? ('#' . $line->product_id);
    $variationName = $line->productVariation->name ?? null;
@endphp

<tr class="hover:bg-gray-50">
    <td class="px-6 py-3 text-sm font-medium text-gray-900">
        {{ $productName }}
    </td>

    <td class="px-6 py-3 text-sm text-gray-700">
        {{ $variationName ?? '—' }}
    </td>

    <td class="px-6 py-3 text-right">
        @if($order->status === 'draft')
            <input
                type="number"
                min="0"
                class="js-line-qty w-28 rounded-md border border-gray-300 px-3 py-2 text-sm
                       focus:border-blue-500 focus:ring-1 focus:ring-blue-500"
                data-product-id="{{ $line->product_id }}"
                data-variation-id="{{ $line->product_variation_id }}"
                value="{{ $line->requested_quantity }}"
            >
        @else
            <span class="text-sm font-semibold text-gray-900">
                {{ $line->requested_quantity }}
            </span>
        @endif
    </td>
</tr>

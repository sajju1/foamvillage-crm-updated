@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-6 py-8">

    {{-- ================= HEADER ================= --}}
    <div class="mb-6">
        <a href="{{ route('orders.show', [$customer, $order]) }}{{ request('context') === 'staff' ? '?context=staff' : '' }}"
           class="crm-btn-secondary">
            ← Back to Order
        </a>
    </div>

    <div class="text-center mb-8">
        <div class="text-xs uppercase tracking-wider text-gray-500">
            Order
        </div>
        <h1 class="text-2xl font-semibold text-gray-900 mt-1">
            {{ $order->order_number }}
        </h1>
        @if(request('mode') === 'amend')
            <p class="mt-2 text-sm text-blue-600 font-medium">
                Amend Mode — adjusting existing order items
            </p>
        @endif
    </div>

    <form method="POST"
          action="{{ route('orders.add-products.store', [$customer, $order]) }}{{ request('context') === 'staff' ? '?context=staff' : '' }}">
        @csrf

        {{-- ================= SCROLLABLE BODY ================= --}}
        <div class="max-h-[65vh] overflow-y-auto border-y border-gray-200 pr-2">

            @foreach ($categories as $category)
                @php
                    $collapseId = 'cat-' . $category->id;
                @endphp

                <div class="mb-6">

                    {{-- CATEGORY HEADER --}}
                    <div
                        class="category-toggle flex items-center justify-between
                               bg-gray-100 border-l-4 border-blue-600
                               px-4 py-3 cursor-pointer"
                        data-target="{{ $collapseId }}"
                    >
                        <h3 class="font-semibold text-gray-800">
                            {{ $category->category_name ?? $category->name }}
                        </h3>
                        <span class="chevron text-sm transition-transform">▼</span>
                    </div>

                    {{-- CATEGORY BODY --}}
                    <div id="{{ $collapseId }}" class="category-body p-4 space-y-6">

                        @foreach ($category->products as $product)

                            @php
                                $existingSimpleLine = $order->orderLines
                                    ->first(fn ($l) =>
                                        $l->product_id === $product->id
                                        && $l->product_variation_id === null
                                    );
                            @endphp

                            {{-- ================= PRODUCTS WITH VARIATIONS ================= --}}
                            @if ($product->variations->count() > 0)

                                <div class="border rounded-lg bg-white">
                                    <div class="px-4 py-3 font-semibold border-b">
                                        {{ $product->product_name }}
                                    </div>

                                    <table class="min-w-full">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-4 py-2 w-20">Select</th>
                                                <th class="px-4 py-2 text-left">Variation</th>
                                                <th class="px-4 py-2 text-right w-48">Qty</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($product->variations as $variation)
                                                @php
                                                    $existingLine = $order->orderLines
                                                        ->first(fn ($l) =>
                                                            $l->product_id === $product->id
                                                            && $l->product_variation_id === $variation->id
                                                        );
                                                @endphp
                                                <tr class="border-t">
                                                    <td class="px-4 py-2">
                                                        <input type="checkbox"
                                                               class="js-pick"
                                                               data-target="qty-{{ $product->id }}-{{ $variation->id }}"
                                                               {{ $existingLine ? 'checked' : '' }}>
                                                    </td>
                                                    <td class="px-4 py-2">
                                                        {{ $variation->display_name }}
                                                    </td>
                                                    <td class="px-4 py-2 text-right">
                                                        <input
                                                            id="qty-{{ $product->id }}-{{ $variation->id }}"
                                                            type="number"
                                                            min="0"
                                                            class="w-36 form-control {{ $existingLine ? '' : 'hidden' }}"
                                                            name="items[{{ $product->id }}_{{ $variation->id }}][requested_quantity]"
                                                            value="{{ $existingLine?->requested_quantity ?? '' }}"
                                                        >
                                                        <input type="hidden"
                                                               name="items[{{ $product->id }}_{{ $variation->id }}][product_id]"
                                                               value="{{ $product->id }}">
                                                        <input type="hidden"
                                                               name="items[{{ $product->id }}_{{ $variation->id }}][product_variation_id]"
                                                               value="{{ $variation->id }}">
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>

                            {{-- ================= SIMPLE PRODUCTS ================= --}}
                            @elseif ($product->variations->count() === 0 && !$product->is_formula_based)

                                <div class="border rounded-lg bg-white">
                                    <div class="px-4 py-3 font-semibold border-b">
                                        {{ $product->product_name }}
                                    </div>

                                    <div class="flex items-center justify-between px-4 py-3">
                                        <label class="flex items-center gap-3">
                                            <input type="checkbox"
                                                   class="js-pick"
                                                   data-target="qty-{{ $product->id }}-simple"
                                                   {{ $existingSimpleLine ? 'checked' : '' }}>
                                            <span class="text-sm text-gray-700">
                                                Standard
                                            </span>
                                        </label>

                                        <input
                                            id="qty-{{ $product->id }}-simple"
                                            type="number"
                                            min="0"
                                            class="w-36 form-control {{ $existingSimpleLine ? '' : 'hidden' }}"
                                            name="items[{{ $product->id }}_0][requested_quantity]"
                                            value="{{ $existingSimpleLine?->requested_quantity ?? '' }}"
                                        >

                                        <input type="hidden"
                                               name="items[{{ $product->id }}_0][product_id]"
                                               value="{{ $product->id }}">
                                        <input type="hidden"
                                               name="items[{{ $product->id }}_0][product_variation_id]"
                                               value="">
                                    </div>
                                </div>

                            {{-- ================= FORMULA PRODUCTS (DISABLED) ================= --}}
                            @else
                                <div class="border rounded-lg bg-gray-50 opacity-70">
                                    <div class="px-4 py-3 font-semibold">
                                        {{ $product->product_name }}
                                    </div>
                                    <div class="px-4 py-3 text-sm text-gray-500">
                                        Formula-based product — configure pricing before ordering.
                                    </div>
                                </div>
                            @endif

                        @endforeach

                    </div>
                </div>
            @endforeach
        </div>

        {{-- ================= FOOTER ================= --}}
        <div class="sticky bottom-0 bg-white border-t py-4 mt-6 flex justify-center">
            <button type="submit" class="crm-btn-primary px-10">
                Save Changes
            </button>
        </div>

    </form>
</div>

{{-- ================= JS ================= --}}
<script>
document.querySelectorAll('.js-pick').forEach(cb => {
    cb.addEventListener('change', function () {
        const qty = document.getElementById(this.dataset.target);
        if (!qty) return;

        if (this.checked) {
            qty.classList.remove('hidden');
            if (!qty.value) qty.value = 1;
            qty.focus();
        } else {
            qty.value = '';
            qty.classList.add('hidden');
        }
    });
});

document.querySelectorAll('.category-toggle').forEach(header => {
    header.addEventListener('click', function () {
        const body = document.getElementById(this.dataset.target);
        const arrow = this.querySelector('.chevron');
        body.classList.toggle('hidden');
        arrow.style.transform = body.classList.contains('hidden')
            ? 'rotate(-90deg)'
            : 'rotate(0deg)';
    });
});
</script>
@endsection

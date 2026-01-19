<div
    x-show="open"
    x-cloak
    x-data="{
        reset() {
            this.$el.querySelectorAll('input[type=checkbox]').forEach(i => i.checked = false);
            this.$el.querySelectorAll('input[type=number]').forEach(i => i.value = '');
            this.$el.querySelectorAll('select').forEach(s => s.value = '');
            this.$el.querySelectorAll('[data-expand]').forEach(e => {
                if (e.__x) e.__x.$data.expanded = false;
            });
        }
    }"
    @click.self="open = false; reset()"
    @keydown.escape.window="open = false; reset()"
    class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
>
    <div class="bg-white w-full max-w-4xl rounded-lg shadow-lg overflow-hidden">

        {{-- Header --}}
        <div class="px-6 py-4 border-b flex justify-between items-center">
            <h2 class="text-lg font-semibold">
                Add Products to Customer Portfolio
            </h2>

            <button
                type="button"
                @click="open = false; reset()"
                class="text-gray-400 hover:text-gray-600"
            >
                ✕
            </button>
        </div>

        {{-- FORM --}}
        <form
            method="POST"
            action="{{ route('customers.portfolio.store', $customer) }}"
        >
            @csrf

            {{-- Body --}}
            <div class="p-6 space-y-4 max-h-[70vh] overflow-y-auto">

                {{-- Product list --}}
                <div class="space-y-4">

                    @foreach ($products as $product)

                        {{-- ========================= --}}
                        {{-- VARIANT-BASED PRODUCT --}}
                        {{-- ========================= --}}
                        @if ($product->product_type === 'variant_based')
                            <div
                                class="border rounded"
                                x-data="{ expanded: false }"
                                data-expand
                            >
                                {{-- Parent header --}}
                                <button
                                    type="button"
                                    @click="expanded = !expanded"
                                    class="w-full flex items-center justify-between px-4 py-2 bg-gray-100 hover:bg-gray-200"
                                >
                                    <div class="font-medium text-left">
                                        {{ $product->product_name }}
                                        <span class="text-xs text-gray-500 ml-2">
                                            (variant-based)
                                        </span>
                                    </div>

                                    <span
                                        class="text-gray-500 text-sm"
                                        x-text="expanded ? '−' : '+'"
                                    ></span>
                                </button>

                                {{-- Variants --}}
                                <div
                                    x-show="expanded"
                                    x-collapse
                                    class="p-4 space-y-2"
                                >
                                    @foreach ($product->variations as $variation)

                                        @php
                                            $variantKey = 'v_' . $variation->id;
                                            $alreadyAdded = isset($activePortfolioMap[$variantKey]);
                                        @endphp

                                        <div
                                            class="flex items-center gap-3"
                                            x-data="{ selected: false }"
                                        >
                                            <input
                                                type="checkbox"
                                                {{ $alreadyAdded ? 'disabled' : '' }}
                                                x-model="selected"
                                                x-bind:name="selected ? 'items[{{ $variantKey }}][product_variation_id]' : null"
                                                value="{{ $variation->id }}"
                                            >

                                            <span class="text-sm {{ $alreadyAdded ? 'text-gray-400' : '' }}">
                                                {{ $variation->length }} ×
                                                {{ $variation->width }} ×
                                                {{ $variation->thickness }}
                                            </span>

                                            @if ($alreadyAdded)
                                                <span class="ml-auto text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                                    Already in portfolio
                                                </span>
                                            @else
                                                <input
                                                    type="number"
                                                    step="0.01"
                                                    min="0"
                                                    x-bind:name="selected ? 'items[{{ $variantKey }}][agreed_price]' : null"
                                                    placeholder="Agreed price"
                                                    class="ml-auto w-32 border rounded px-2 py-1 text-sm"
                                                    x-bind:disabled="!selected"
                                                    x-bind:required="selected"
                                                    x-bind:class="!selected ? 'opacity-50' : ''"
                                                >
                                            @endif

                                            <input type="hidden" name="items[{{ $variantKey }}][product_id]" value="{{ $product->id }}">
                                            <input type="hidden" name="items[{{ $variantKey }}][pricing_type]" value="fixed">
                                        </div>

                                    @endforeach
                                </div>
                            </div>

                        {{-- ========================= --}}
                        {{-- FORMULA-BASED PRODUCT --}}
                        {{-- ========================= --}}
                        @elseif ($product->product_type === 'formula_based')
                            @php
                                $productKey = 'f_' . $product->id;
                                $alreadyAdded = isset($activePortfolioMap[$productKey]);
                            @endphp

                            <div
                                class="border rounded p-4 space-y-3"
                                x-data="{ selected: false }"
                            >
                                <div class="flex items-center gap-3">
                                    <input
                                        type="checkbox"
                                        {{ $alreadyAdded ? 'disabled' : '' }}
                                        x-model="selected"
                                    >

                                    <span class="text-sm font-medium {{ $alreadyAdded ? 'text-gray-400' : '' }}">
                                        {{ $product->product_name }}
                                        <span class="text-xs text-gray-500 ml-1">(formula-based)</span>
                                    </span>

                                    @if ($alreadyAdded)
                                        <span class="ml-auto text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                            Already in portfolio
                                        </span>
                                    @endif
                                </div>

                                @if (!$alreadyAdded)
                                    <div x-show="selected" x-collapse class="ml-6 space-y-2">

                                        <input type="hidden" name="items[{{ $productKey }}][product_id]" value="{{ $product->id }}">
                                        <input type="hidden" name="items[{{ $productKey }}][pricing_type]" value="formula">

                                        <div>
                                            <label class="block text-xs text-gray-500 mb-1">
                                                Pricing mode
                                            </label>
                                            <select
                                                name="items[{{ $productKey }}][formula_pricing_mode]"
                                                class="border rounded px-2 py-1 text-sm w-full"
                                                x-bind:required="selected"
                                            >
                                                <option value="standard">Use standard formula</option>
                                                <option value="rate_override">Override rate</option>
                                                <option value="percentage_modifier">Apply percentage modifier</option>
                                            </select>
                                        </div>

                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                            <input
                                                type="number"
                                                step="0.01"
                                                name="items[{{ $productKey }}][rate_override]"
                                                placeholder="Rate override"
                                                class="border rounded px-2 py-1 text-sm"
                                            >

                                            <input
                                                type="number"
                                                step="0.01"
                                                name="items[{{ $productKey }}][percentage_modifier]"
                                                placeholder="Percentage modifier (e.g. -10)"
                                                class="border rounded px-2 py-1 text-sm"
                                            >

                                            <input
                                                type="number"
                                                step="0.01"
                                                name="items[{{ $productKey }}][minimum_charge]"
                                                placeholder="Minimum charge"
                                                class="border rounded px-2 py-1 text-sm"
                                            >

                                            <select
                                                name="items[{{ $productKey }}][rounding_rule]"
                                                class="border rounded px-2 py-1 text-sm"
                                            >
                                                <option value="">No rounding</option>
                                                <option value="none">None</option>
                                                <option value="nearest_0.5">Nearest 0.5</option>
                                                <option value="nearest_1">Nearest 1</option>
                                            </select>
                                        </div>
                                    </div>
                                @endif
                            </div>

                        {{-- ========================= --}}
                        {{-- SIMPLE PRODUCT --}}
                        {{-- ========================= --}}
                        @else
                            @php
                                $productKey = 'p_' . $product->id;
                                $alreadyAdded = isset($activePortfolioMap[$productKey]);
                            @endphp

                            <div
                                class="border rounded p-4 flex items-center gap-3"
                                x-data="{ selected: false }"
                            >
                                <input
                                    type="checkbox"
                                    {{ $alreadyAdded ? 'disabled' : '' }}
                                    x-model="selected"
                                    x-bind:name="selected ? 'items[{{ $productKey }}][product_id]' : null"
                                    value="{{ $product->id }}"
                                >

                                <span class="text-sm font-medium {{ $alreadyAdded ? 'text-gray-400' : '' }}">
                                    {{ $product->product_name }}
                                </span>

                                @if ($alreadyAdded)
                                    <span class="ml-auto text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                        Already in portfolio
                                    </span>
                                @else
                                    <input
                                        type="number"
                                        step="0.01"
                                        min="0"
                                        x-bind:name="selected ? 'items[{{ $productKey }}][agreed_price]' : null"
                                        placeholder="Agreed price"
                                        class="ml-auto w-32 border rounded px-2 py-1 text-sm"
                                        x-bind:disabled="!selected"
                                        x-bind:required="selected"
                                        x-bind:class="!selected ? 'opacity-50' : ''"
                                    >
                                @endif

                                <input type="hidden" name="items[{{ $productKey }}][pricing_type]" value="fixed">
                            </div>
                        @endif

                    @endforeach

                </div>
            </div>

            {{-- Footer --}}
            <div class="px-6 py-4 border-t flex justify-end gap-3">
                <button
                    type="button"
                    @click="open = false; reset()"
                    class="px-4 py-2 text-sm border rounded"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="px-4 py-2 text-sm bg-blue-600 text-white rounded"
                >
                    Add to Portfolio
                </button>
            </div>

        </form>

    </div>
</div>

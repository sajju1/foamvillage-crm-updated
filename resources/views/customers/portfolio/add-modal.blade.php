{{-- MODAL WRAPPER (USES PARENT `open`) --}}
<div x-show="open" x-cloak>

    {{-- OVERLAY --}}
    <div class="fixed inset-0 z-40 bg-black bg-opacity-50"
         @click="open = false"></div>

    {{-- MODAL --}}
    <div class="fixed inset-0 z-50 flex items-center justify-center">

        <div class="bg-white w-full max-w-5xl max-h-[85vh] rounded shadow-lg flex flex-col"
             @click.stop>

            {{-- HEADER --}}
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h2 class="text-lg font-semibold">
                    Add Products to Portfolio
                </h2>

                <button type="button"
                        class="text-gray-500 hover:text-gray-700 text-xl"
                        @click="open = false">
                    ×
                </button>
            </div>

            {{-- BODY --}}
            <div class="p-6 overflow-y-auto flex-1"
                 x-data="{ search: '' }">

                {{-- SEARCH --}}
                <div class="mb-4">
                    <input type="text"
                           x-model="search"
                           placeholder="Search products or variants…"
                           class="w-full border rounded px-3 py-2 text-sm focus:outline-none focus:ring">
                </div>

                <form method="POST" action="{{ route('customers.portfolio.store', $customer) }}">
                    @csrf

                    <div class="space-y-6">

                        @foreach ($products as $product)
                            @php
                                $type = $product->product_type;
                                $isFormula = in_array($type, ['rule_based', 'formula_based']);

                                $searchText = strtolower(
                                    $product->product_name . ' ' .
                                    str_replace('_', ' ', $type) . ' ' .
                                    $product->variations
                                        ->map(fn($v) => "{$v->length} {$v->width} {$v->thickness}")
                                        ->implode(' ')
                                );
                            @endphp

                            <div class="border rounded-md"
                                 data-search="{{ $searchText }}"
                                 x-show="search === '' || $el.dataset.search.includes(search.toLowerCase())">

                                {{-- PRODUCT HEADER --}}
                                <div class="px-4 py-3 bg-gray-100">
                                    <div class="font-semibold">
                                        {{ $product->product_name }}
                                    </div>
                                    <div class="text-xs text-gray-500">
                                        {{ str_replace('_', ' ', $type) }}
                                    </div>
                                </div>

                                {{-- SIMPLE & FORMULA --}}
                                @if ($type !== 'variant_based')
                                    @php
                                        $key = 'p' . $product->id . '_v0';
                                        $mapKey = 'product_' . $product->id;
                                        $alreadyAdded = $activePortfolioMap->has($mapKey);
                                    @endphp

                                    <div x-data="{ selected: false }"
                                         class="px-4 py-3 flex items-center justify-between {{ $alreadyAdded ? 'opacity-50' : '' }}">

                                        <label class="flex items-center gap-2 text-sm">
                                            <input type="checkbox"
                                                   x-model="selected"
                                                   @disabled($alreadyAdded)>
                                            Add product
                                        </label>

                                        {{-- HIDDEN FIELDS --}}
                                        <input type="hidden"
                                               name="items[{{ $key }}][product_id]"
                                               value="{{ $product->id }}"
                                               :disabled="!selected || {{ $alreadyAdded ? 'true' : 'false' }}">

                                        <input type="hidden"
                                               name="items[{{ $key }}][product_variation_id]"
                                               value=""
                                               :disabled="!selected || {{ $alreadyAdded ? 'true' : 'false' }}">

                                        <input type="hidden"
                                               name="items[{{ $key }}][pricing_type]"
                                               value="{{ $isFormula ? 'formula' : 'fixed' }}"
                                               :disabled="!selected || {{ $alreadyAdded ? 'true' : 'false' }}">

                                        {{-- RIGHT SIDE --}}
                                        @if ($alreadyAdded)
                                            <span class="text-xs text-gray-500 italic">
                                                Already in portfolio
                                            </span>
                                        @elseif ($isFormula)
                                            <span class="text-sm italic text-gray-500">
                                                Formula pricing
                                            </span>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <label class="text-sm">Price</label>
                                                <input type="number"
                                                       step="0.01"
                                                       min="0"
                                                       name="items[{{ $key }}][agreed_price]"
                                                       class="border rounded px-2 py-1 w-28"
                                                       :disabled="!selected"
                                                       :required="selected">
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- VARIANTS --}}
                                @if ($type === 'variant_based')
                                    <div class="divide-y">
                                        @foreach ($product->variations as $variation)
                                            @php
                                                $vKey = 'p' . $product->id . '_v' . $variation->id;
                                                $variantMapKey = 'product_' . $product->id . '_variant_' . $variation->id;
                                                $alreadyAdded = $activePortfolioMap->has($variantMapKey);
                                            @endphp

                                            <div x-data="{ selected: false }"
                                                 class="px-4 py-3 flex items-center justify-between {{ $alreadyAdded ? 'opacity-50' : '' }}">

                                                <div class="text-sm font-medium">
                                                    {{ $variation->length }} ×
                                                    {{ $variation->width }} ×
                                                    {{ $variation->thickness }}
                                                </div>

                                                <div class="flex items-center gap-3">
                                                    <label class="flex items-center gap-2 text-sm">
                                                        <input type="checkbox"
                                                               x-model="selected"
                                                               @disabled($alreadyAdded)>
                                                        Add
                                                    </label>

                                                    {{-- HIDDEN FIELDS --}}
                                                    <input type="hidden"
                                                           name="items[{{ $vKey }}][product_id]"
                                                           value="{{ $product->id }}"
                                                           :disabled="!selected || {{ $alreadyAdded ? 'true' : 'false' }}">

                                                    <input type="hidden"
                                                           name="items[{{ $vKey }}][product_variation_id]"
                                                           value="{{ $variation->id }}"
                                                           :disabled="!selected || {{ $alreadyAdded ? 'true' : 'false' }}">

                                                    <input type="hidden"
                                                           name="items[{{ $vKey }}][pricing_type]"
                                                           value="fixed"
                                                           :disabled="!selected || {{ $alreadyAdded ? 'true' : 'false' }}">

                                                    @if ($alreadyAdded)
                                                        <span class="text-xs text-gray-500 italic">
                                                            Already in portfolio
                                                        </span>
                                                    @else
                                                        <div class="flex items-center gap-2">
                                                            <label class="text-sm">Price</label>
                                                            <input type="number"
                                                                   step="0.01"
                                                                   min="0"
                                                                   name="items[{{ $vKey }}][agreed_price]"
                                                                   class="border rounded px-2 py-1 w-28"
                                                                   :disabled="!selected"
                                                                   :required="selected">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif

                            </div>
                        @endforeach
                    </div>

                    {{-- FOOTER --}}
                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button"
                                class="px-4 py-2 border rounded"
                                @click="open = false">
                            Cancel
                        </button>

                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
                            Add Selected Products
                        </button>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>

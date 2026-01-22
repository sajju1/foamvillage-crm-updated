{{-- DEBUG --}}
<div class="mb-2 text-xs text-red-600">
    Portfolio partial loaded
</div>

<div class="mt-6">
    <h3 class="text-lg font-semibold mb-4">Customer Product Portfolio</h3>

    {{-- Toggle: Show / Hide Standard Price --}}
    <form method="GET" class="mb-3">
        <label class="inline-flex items-center gap-2">
            <input type="checkbox" name="show_standard_price" value="1"
                {{ request('show_standard_price') ? 'checked' : '' }}>
            <span>Show standard product price</span>
        </label>
    </form>

    <table class="min-w-full border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Item</th>

                @if (request('show_standard_price'))
                    <th class="px-4 py-2 text-left">Standard Price</th>
                @endif

                <th class="px-4 py-2 text-left">Customer Price</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>

        <tbody>
            @forelse($customer->productPortfolio as $item)
                <tr class="border-t">
                    {{-- Item --}}
                    <td class="px-4 py-2">
                        {{ $item->sellable_label }}
                    </td>

                    {{-- Standard Price (optional) --}}
                    @if (request('show_standard_price'))
                        <td class="px-4 py-2">
                            @if ($item->product?->base_price)
                                £{{ number_format($item->product->base_price, 2) }}
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    @endif

                    {{-- Customer Price --}}
                    <td class="px-4 py-2">
                        @if ($item->pricing_type === 'fixed')
                            £{{ number_format($item->agreed_price, 2) }}
                        @else
                            <span class="italic text-gray-500">Formula pricing</span>
                        @endif
                    </td>

                    {{-- Status --}}
                    <td class="px-4 py-2">
                        @if ($item->is_active)
                            <span class="text-green-600 font-semibold">Active</span>
                        @else
                            <span class="text-gray-500">Inactive</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td class="px-4 py-2 text-right">
                        <button class="text-red-600 hover:underline">
                            Deactivate
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="{{ request('show_standard_price') ? 5 : 4 }}"
                        class="px-4 py-6 text-center text-gray-500">
                        No products in portfolio.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

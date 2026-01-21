<div class="mt-6">
    <h3 class="text-lg font-semibold mb-4">Customer Product Portfolio</h3>

    <table class="min-w-full border border-gray-200">
        <thead class="bg-gray-100">
            <tr>
                <th class="px-4 py-2 text-left">Item</th>
                <th class="px-4 py-2 text-left">Pricing</th>
                <th class="px-4 py-2 text-left">Status</th>
                <th class="px-4 py-2 text-right">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customer->productPortfolio as $item)
                <tr class="border-t">
                    <td class="px-4 py-2">
                        {{ $item->sellable_label }}
                    </td>

                    <td class="px-4 py-2">
                        @if($item->pricing_type === 'fixed')
                            £{{ number_format($item->agreed_price, 2) }}
                        @else
                            <span class="italic text-gray-500">Formula pricing</span>
                        @endif
                    </td>

                    <td class="px-4 py-2">
                        @if($item->is_active)
                            <span class="text-green-600 font-semibold">Active</span>
                        @else
                            <span class="text-gray-500">Inactive</span>
                        @endif
                    </td>

                    <td class="px-4 py-2 text-right">
                        <button class="text-red-600 hover:underline">
                            Deactivate
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-4 py-6 text-center text-gray-500">
                        No products in portfolio.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

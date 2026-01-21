<div x-data="{ open: false }" class="bg-white p-6 rounded shadow space-y-4">

    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold">
            Customer Product Portfolio
        </h2>

        <button
            type="button"
            class="bg-blue-600 text-white px-3 py-1 rounded text-sm"
            @click="open = true"
        >
            + Add Product
        </button>
    </div>

    @if ($customer->portfolio->count() === 0)
        <div class="text-sm text-gray-600 bg-gray-50 border rounded p-4">
            No products assigned to this customer yet.
        </div>
    @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Item</th>
                        <th class="p-2 text-right">Customer Pricing</th>
                        <th class="p-2 text-center">Status</th>
                        <th class="p-2 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($customer->portfolio as $entry)
                        <tr class="border-t">
                            <td class="p-2 font-medium">
                                {{ $entry->sellable_label }}
                            </td>

                            <td class="p-2 text-right">
                                @if ($entry->pricing_type === 'fixed')
                                    £{{ number_format($entry->agreed_price, 2) }}
                                @else
                                    <span class="italic text-gray-500">
                                        Formula pricing
                                    </span>
                                @endif
                            </td>

                            <td class="p-2 text-center">
                                @if ($entry->is_active)
                                    <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                        Active
                                    </span>
                                @else
                                    <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                        Inactive
                                    </span>
                                @endif
                            </td>

                            <td class="p-2 text-right">
                                @if ($entry->is_active)
                                    <form method="POST"
                                          action="{{ route('customers.portfolio.deactivate', $entry) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-600 text-sm">
                                            Deactivate
                                        </button>
                                    </form>
                                @else
                                    <span class="text-xs text-gray-400">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- MODAL (shares Alpine state) --}}
    <div x-show="open" x-cloak>
        @include('customers.portfolio.add-modal')
    </div>

</div>

<div class="bg-white p-6 rounded shadow space-y-4">

    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold">
            Customer Product Portfolio
        </h2>

        <a href="#"
           class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
            + Add Product
        </a>
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
                        <th class="p-2 text-left">Product</th>
                        <th class="p-2 text-left">Category</th>
                        <th class="p-2 text-right">Customer Price</th>
                        <th class="p-2 text-right text-gray-500">
                            Standard Price
                        </th>
                        <th class="p-2 text-center">Status</th>
                        <th class="p-2 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($customer->portfolio as $entry)
                        <tr class="border-t">
                            <td class="p-2">
                                {{ $entry->product->name }}
                            </td>

                            <td class="p-2 text-gray-600">
                                {{ $entry->product->category->name ?? '—' }}
                            </td>

                            <td class="p-2 text-right font-medium">
                                {{ number_format($entry->customer_price, 2) }}
                            </td>

                            <td class="p-2 text-right text-gray-400">
                                {{ number_format($entry->product->price ?? 0, 2) }}
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
                                    <span class="text-xs text-gray-400">
                                        —
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    @endif

</div>

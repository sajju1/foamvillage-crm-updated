{{-- OFFER MODAL --}}
<div x-show="showOffers" x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center">

    {{-- OVERLAY --}}
    <div class="fixed inset-0 bg-black bg-opacity-50"
         @click="showOffers = false"></div>

    {{-- MODAL BOX --}}
    <div class="bg-white w-full max-w-lg rounded shadow-lg p-6 relative"
         @click.stop>

        <h3 class="text-lg font-semibold mb-1">
            Offers
        </h3>

        <p class="text-sm text-gray-600 mb-4">
            {{ $entry->sellable_label }}
        </p>

        {{-- EXISTING OFFERS --}}
        @if ($entry->offers->count() === 0)
            <div class="border rounded p-4 text-sm text-gray-600 bg-gray-50 mb-4">
                No offers configured for this product yet.
            </div>
        @else
            <div class="space-y-3 mb-4">
                @foreach ($entry->offers as $offer)
                    <div class="border rounded p-3 text-sm">
                        <div class="flex justify-between items-center">
                            <div>
                                <div class="font-medium">
                                    {{ ucfirst(str_replace('_', ' ', $offer->offer_type)) }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $offer->effective_from->format('d M Y') }}
                                    @if ($offer->effective_to)
                                        → {{ $offer->effective_to->format('d M Y') }}
                                    @endif
                                </div>
                            </div>

                            @if ($offer->is_active)
                                <span class="text-xs bg-green-100 text-green-800 px-2 py-1 rounded">
                                    Active
                                </span>
                            @else
                                <span class="text-xs bg-gray-200 text-gray-700 px-2 py-1 rounded">
                                    Inactive
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

        {{-- ADD OFFER FORM --}}
        <form method="POST"
              action="{{ route('customers.portfolio.offers.store', $entry) }}"
              class="space-y-3 border-t pt-4">
            @csrf

            <div>
                <label class="text-sm font-medium">Offer type</label>
                <select name="offer_type" required
                        class="border rounded w-full px-2 py-1 text-sm">
                    <option value="fixed_price">Fixed price</option>
                    <option value="percentage_discount">Percentage discount</option>
                    <option value="fixed_discount">Fixed discount</option>
                </select>
            </div>

            <div>
                <label class="text-sm font-medium">Fixed price (£)</label>
                <input type="number" name="fixed_price" step="0.01"
                       class="border rounded w-full px-2 py-1 text-sm">
            </div>

            <div>
                <label class="text-sm font-medium">Percentage (%)</label>
                <input type="number" name="percentage" step="0.01"
                       class="border rounded w-full px-2 py-1 text-sm">
            </div>

            <div>
                <label class="text-sm font-medium">Discount amount (£)</label>
                <input type="number" name="discount_amount" step="0.01"
                       class="border rounded w-full px-2 py-1 text-sm">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-sm font-medium">Effective from</label>
                    <input type="date" name="effective_from" required
                           class="border rounded w-full px-2 py-1 text-sm">
                </div>

                <div>
                    <label class="text-sm font-medium">Effective to</label>
                    <input type="date" name="effective_to"
                           class="border rounded w-full px-2 py-1 text-sm">
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button"
                        class="px-3 py-1 border rounded text-sm"
                        @click="showOffers = false">
                    Close
                </button>

                <button type="submit"
                        class="px-3 py-1 bg-blue-600 text-white rounded text-sm">
                    Add Offer
                </button>
            </div>
        </form>

    </div>
</div>

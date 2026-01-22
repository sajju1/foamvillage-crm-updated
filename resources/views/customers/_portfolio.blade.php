<div x-data="{ open: false }" class="bg-white p-6 rounded shadow space-y-4">

    <div class="flex justify-between items-center">
        <h2 class="text-lg font-semibold">
            Customer Product Portfolio
        </h2>

        <button type="button" class="bg-blue-600 text-white px-3 py-1 rounded text-sm" @click="open = true">
            + Add Product
        </button>
    </div>

    {{-- Portfolio Actions --}}
    <div class="flex items-center gap-2 mb-4">

        <a href="{{ route('customers.portfolio.print', $customer) }}" target="_blank"
            class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">
            🖨 Print
        </a>

        <a href="{{ route('customers.portfolio.pdf', $customer) }}" target="_blank"
            class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300">
            📄 Download PDF
        </a>

        <a href="{{ route('customers.portfolio.email', $customer) }}"
            class="px-3 py-2 text-sm bg-gray-200 rounded hover:bg-gray-300"
            onclick="return confirm('Email portfolio sheet to customer?')">
            ✉ Email
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
                        <th class="p-2 text-left">Item</th>
                        <th class="p-2 text-right">Product Standard Price</th>
                        <th class="p-2 text-right">Customer Pricing</th>
                        <th class="p-2 text-center">Offers</th>
                        <th class="p-2 text-center">Status</th>
                        <th class="p-2 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @foreach ($customer->portfolio as $entry)
                        <tr class="border-t" x-data="{ editing: false, showOffers: false }">

                            {{-- ITEM --}}
                            <td class="p-2 font-medium">
                                {{ $entry->sellable_label }}
                            </td>

                            {{-- STANDARD PRICE --}}
                            <td class="p-2 text-right">
                                @if ($entry->variation && isset($entry->variation->standard_price))
                                    £{{ number_format($entry->variation->standard_price, 2) }}
                                @elseif ($entry->product && isset($entry->product->simple_price))
                                    £{{ number_format($entry->product->simple_price, 2) }}
                                @else
                                    <span class="text-gray-400">—</span>
                                @endif
                            </td>

                            {{-- CUSTOMER PRICE --}}
                            <td class="p-2 text-right">
                                @if ($entry->pricing_type === 'fixed')
                                    £{{ number_format($entry->agreed_price, 2) }}
                                @else
                                    <span class="italic text-gray-500">
                                        Formula pricing
                                    </span>
                                @endif
                            </td>

                            {{-- OFFERS --}}
                            <td class="p-2 text-center space-y-1">

                                @php
                                    $activeOffer = $entry->offers
                                        ->where('is_active', true)
                                        ->sortByDesc('effective_from')
                                        ->first();
                                @endphp

                                @if ($activeOffer)
                                    <div class="text-xs font-medium text-green-700">
                                        @switch($activeOffer->offer_type)
                                            @case('fixed_price')
                                                £{{ number_format($activeOffer->fixed_price, 2) }}
                                            @break

                                            @case('percentage_discount')
                                                {{ $activeOffer->percentage }}% off
                                            @break

                                            @case('fixed_discount')
                                                £{{ number_format($activeOffer->discount_amount, 2) }} off
                                            @break
                                        @endswitch
                                    </div>

                                    <div class="text-[11px] text-gray-500">
                                        @if ($activeOffer->effective_to)
                                            Until {{ $activeOffer->effective_to->format('d M Y') }}
                                        @else
                                            No expiry
                                        @endif
                                    </div>
                                @else
                                    <div class="text-xs text-gray-400">—</div>
                                @endif

                                {{-- OPEN MODAL --}}
                                <button type="button" class="text-blue-600 text-xs underline"
                                    @click="showOffers = true">
                                    View / Add
                                </button>

                                {{-- 🔴 OFFER MODAL MUST BE INSIDE THIS ROW --}}
                                @include('customers._offers', ['entry' => $entry])

                            </td>

                            {{-- STATUS --}}
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

                            {{-- ACTIONS --}}
                            <td class="p-2 text-right">
                                <form method="POST" action="{{ route('customers.portfolio.deactivate', $entry) }}">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 text-sm">
                                        Deactivate
                                    </button>
                                </form>
                            </td>

                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

    {{-- ADD PRODUCT MODAL --}}
    <div x-show="open" x-cloak>
        @include('customers.portfolio.add-modal')
    </div>

</div>

<div class="bg-white p-6 rounded shadow space-y-8">

    <h2 class="text-lg font-semibold border-b pb-2">
        Addresses
    </h2>

    @php
        $registered = $customer->addresses
            ->where('address_type', 'registered')
            ->where('is_active', true)
            ->first();

        $billing = $customer->addresses
            ->where('address_type', 'billing')
            ->where('is_active', true)
            ->first();

        $deliveryActive = $customer->addresses
            ->where('address_type', 'delivery')
            ->where('is_active', true);

        $deliveryInactive = $customer->addresses
            ->where('address_type', 'delivery')
            ->where('is_active', false);
    @endphp

    {{-- ================= REGISTERED ADDRESS ================= --}}
    <div class="space-y-3">
        <h3 class="font-medium">Registered Address <span class="text-red-600">*</span></h3>

        @if ($registered)
            <div class="border rounded p-3 text-sm bg-gray-50">
                {{ $registered->address_line1 }}<br>
                {{ $registered->address_line2 }}<br>
                {{ $registered->city }}<br>
                {{ $registered->postcode }}<br>
                {{ $registered->country }}
            </div>
        @else
            <div class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 p-3 rounded">
                Registered address is required.
            </div>
        @endif

        {{-- Add / Replace Registered --}}
        <form method="POST"
              action="{{ route('customers.addresses.store', $customer) }}"
              class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @csrf
            <input type="hidden" name="address_type" value="registered">

            @include('customers.partials.address_form_fields')

            <button class="bg-blue-600 text-white px-3 py-1 rounded md:col-span-3">
                Save Registered Address
            </button>
        </form>
    </div>

    {{-- ================= BILLING ADDRESS ================= --}}
    <div class="space-y-3">
        <h3 class="font-medium">Billing Address <span class="text-red-600">*</span></h3>

        @if ($billing)
            <div class="border rounded p-3 text-sm bg-gray-50">
                {{ $billing->address_line1 }}<br>
                {{ $billing->address_line2 }}<br>
                {{ $billing->city }}<br>
                {{ $billing->postcode }}<br>
                {{ $billing->country }}
            </div>
        @else
            <div class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 p-3 rounded">
                Billing address is required.
            </div>
        @endif

        {{-- Copy Registered → Billing --}}
        @if ($registered)
            <form method="POST"
                  action="{{ route('customers.addresses.store', $customer) }}"
                  class="mb-2">
                @csrf
                <input type="hidden" name="address_type" value="billing">
                @foreach (['address_line1','address_line2','address_line3','city','state_region','postcode','country'] as $field)
                    <input type="hidden" name="{{ $field }}" value="{{ $registered->$field }}">
                @endforeach

                <button class="text-sm text-blue-600 hover:underline">
                    Copy registered address to billing
                </button>
            </form>
        @endif

        {{-- Add / Replace Billing --}}
        <form method="POST"
              action="{{ route('customers.addresses.store', $customer) }}"
              class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @csrf
            <input type="hidden" name="address_type" value="billing">

            @include('customers.partials.address_form_fields')

            <button class="bg-blue-600 text-white px-3 py-1 rounded md:col-span-3">
                Save Billing Address
            </button>
        </form>
    </div>

    {{-- ================= DELIVERY ADDRESSES ================= --}}
    <div class="space-y-3">
        <h3 class="font-medium">
            Delivery Addresses <span class="text-red-600">*</span>
        </h3>

        @if ($deliveryActive->isEmpty())
            <div class="text-sm text-yellow-700 bg-yellow-50 border border-yellow-200 p-3 rounded">
                At least one delivery address is required.
            </div>
        @else
            <table class="w-full text-sm border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Address</th>
                        <th class="p-2 text-left">Postcode</th>
                        <th class="p-2 text-left">Country</th>
                        <th class="p-2 text-left">Default</th>
                        <th class="p-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($deliveryActive as $delivery)
                        <tr class="border-t">
                            <td class="p-2">
                                {{ $delivery->address_line1 }},
                                {{ $delivery->city }}
                            </td>
                            <td class="p-2">{{ $delivery->postcode }}</td>
                            <td class="p-2">{{ $delivery->country }}</td>
                            <td class="p-2">
                                @if ($delivery->is_default)
                                    <span class="text-xs bg-blue-100 text-blue-800 px-2 py-1 rounded">
                                        Default
                                    </span>
                                @else
                                    —
                                @endif
                            </td>
                            <td class="p-2 text-right">
                                <form method="POST"
                                      action="{{ route('customers.addresses.deactivate', $delivery) }}"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 text-xs">
                                        Deactivate
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif

        {{-- Copy Registered → Delivery --}}
        @if ($registered)
            <form method="POST"
                  action="{{ route('customers.addresses.store', $customer) }}"
                  class="mb-2">
                @csrf
                <input type="hidden" name="address_type" value="delivery">
                @foreach (['address_line1','address_line2','address_line3','city','state_region','postcode','country'] as $field)
                    <input type="hidden" name="{{ $field }}" value="{{ $registered->$field }}">
                @endforeach

                <button class="text-sm text-blue-600 hover:underline">
                    Copy registered address to delivery
                </button>
            </form>
        @endif

        {{-- Add Delivery --}}
        <form method="POST"
              action="{{ route('customers.addresses.store', $customer) }}"
              class="grid grid-cols-1 md:grid-cols-3 gap-3">
            @csrf
            <input type="hidden" name="address_type" value="delivery">

            @include('customers.partials.address_form_fields')

            <label class="flex items-center gap-2 text-sm md:col-span-3">
                <input type="checkbox" name="is_default" value="1">
                Set as default delivery address
            </label>

            <button class="bg-blue-600 text-white px-3 py-1 rounded md:col-span-3">
                Add Delivery Address
            </button>
        </form>

        {{-- Delivery History --}}
        @if ($deliveryInactive->isNotEmpty())
            <details class="text-sm text-gray-600">
                <summary class="cursor-pointer font-medium">
                    Delivery Address History
                </summary>

                <div class="mt-2 space-y-2">
                    @foreach ($deliveryInactive as $address)
                        <div class="border rounded p-2">
                            {{ $address->address_line1 }},
                            {{ $address->city }},
                            {{ $address->postcode }},
                            {{ $address->country }}
                        </div>
                    @endforeach
                </div>
            </details>
        @endif
    </div>

</div>

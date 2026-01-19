@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6 space-y-6">

    <div class="bg-white p-4 rounded shadow flex justify-between">
        <div>
            <h1 class="text-xl font-semibold">
                {{ ucfirst($type) }} Address
            </h1>
            <p class="text-sm text-gray-600">
                Customer: {{ $customer->contact_name }}
                <span class="font-mono">({{ $customer->account_number }})</span>
            </p>
        </div>

        <a href="{{ route('customers.show', $customer) }}"
           class="text-blue-600 hover:underline text-sm">
            ← Back to Customer
        </a>
    </div>

    @php
        $registered = $customer->addresses
            ->where('address_type', 'registered')
            ->where('is_active', true)
            ->first();

        $billing = $customer->addresses
            ->where('address_type', 'billing')
            ->where('is_active', true)
            ->first();
    @endphp

    {{-- ================= COPY OPTIONS ================= --}}
    @if (
        ($type === 'billing' && $registered) ||
        ($type === 'delivery' && ($registered || $billing))
    )
        <div class="bg-gray-50 border rounded p-4 text-sm space-y-2">
            <div class="font-medium">Quick fill</div>

            @if ($type === 'billing' && $registered)
                <form method="POST" action="{{ route('customers.addresses.store', $customer) }}">
                    @csrf
                    <input type="hidden" name="address_type" value="billing">
                    @foreach (['address_line1','address_line2','address_line3','city','state_region','postcode','country'] as $field)
                        <input type="hidden" name="{{ $field }}" value="{{ $registered->$field }}">
                    @endforeach
                    <button class="text-blue-600 hover:underline text-sm">
                        Copy from Registered Address
                    </button>
                </form>
            @endif

            @if ($type === 'delivery')
                @if ($registered)
                    <form method="POST" action="{{ route('customers.addresses.store', $customer) }}">
                        @csrf
                        <input type="hidden" name="address_type" value="delivery">
                        @foreach (['address_line1','address_line2','address_line3','city','state_region','postcode','country'] as $field)
                            <input type="hidden" name="{{ $field }}" value="{{ $registered->$field }}">
                        @endforeach
                        <button class="text-blue-600 hover:underline text-sm">
                            Copy from Registered Address
                        </button>
                    </form>
                @endif

                @if ($billing)
                    <form method="POST" action="{{ route('customers.addresses.store', $customer) }}">
                        @csrf
                        <input type="hidden" name="address_type" value="delivery">
                        @foreach (['address_line1','address_line2','address_line3','city','state_region','postcode','country'] as $field)
                            <input type="hidden" name="{{ $field }}" value="{{ $billing->$field }}">
                        @endforeach
                        <button class="text-blue-600 hover:underline text-sm">
                            Copy from Billing Address
                        </button>
                    </form>
                @endif
            @endif
        </div>
    @endif

    {{-- ================= ADDRESS FORM ================= --}}
    <form method="POST"
          action="{{ route('customers.addresses.store', $customer) }}"
          class="bg-white p-6 rounded shadow space-y-4">
        @csrf

        <input type="hidden" name="address_type" value="{{ $type }}">

        <input name="address_line1" placeholder="Address line 1 *" class="border px-3 py-2 w-full" required>
        <input name="address_line2" placeholder="Address line 2" class="border px-3 py-2 w-full">
        <input name="address_line3" placeholder="Address line 3" class="border px-3 py-2 w-full">

        <input name="city" placeholder="City *" class="border px-3 py-2 w-full" required>
        <input name="state_region" placeholder="State / Region" class="border px-3 py-2 w-full">
        <input name="postcode" placeholder="Postcode *" class="border px-3 py-2 w-full" required>
        <input name="country" placeholder="Country *" class="border px-3 py-2 w-full" required>

        @if ($type === 'delivery')
            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="is_default" value="1">
                Set as default delivery address
            </label>
        @endif

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('customers.show', $customer) }}"
               class="bg-gray-200 px-4 py-2 rounded">
                Cancel
            </a>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Save Address
            </button>
        </div>
    </form>

</div>
@endsection

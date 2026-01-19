@extends('layouts.app')

@section('content')
<div class="max-w-3xl mx-auto py-6 space-y-6">

    <div class="bg-white p-4 rounded shadow flex justify-between">
        <div>
            <h1 class="text-xl font-semibold">
                Edit Delivery Address
            </h1>
            <p class="text-sm text-gray-600">
                Customer: {{ $address->customer->contact_name }}
            </p>
        </div>

        <a href="{{ route('customers.show', $address->customer) }}"
           class="text-blue-600 hover:underline text-sm">
            ← Back to Customer
        </a>
    </div>

    <form method="POST"
          action="{{ route('customers.addresses.update', $address) }}"
          class="bg-white p-6 rounded shadow space-y-4">
        @csrf
        @method('PUT')

        {{-- Address fields --}}
        <input name="address_line1" value="{{ $address->address_line1 }}"
               class="border px-3 py-2 w-full" required>

        <input name="address_line2" value="{{ $address->address_line2 }}"
               class="border px-3 py-2 w-full">

        <input name="address_line3" value="{{ $address->address_line3 }}"
               class="border px-3 py-2 w-full">

        <input name="city" value="{{ $address->city }}"
               class="border px-3 py-2 w-full" required>

        <input name="state_region" value="{{ $address->state_region }}"
               class="border px-3 py-2 w-full">

        <input name="postcode" value="{{ $address->postcode }}"
               class="border px-3 py-2 w-full" required>

        <input name="country" value="{{ $address->country }}"
               class="border px-3 py-2 w-full" required>

        <label class="flex items-center gap-2 text-sm">
            <input type="checkbox" name="is_default" value="1"
                @checked($address->is_default)>
            Set as default delivery address
        </label>

        <div class="flex justify-end gap-3 pt-4">
            <a href="{{ route('customers.show', $address->customer) }}"
               class="bg-gray-200 px-4 py-2 rounded">
                Cancel
            </a>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Update Address
            </button>
        </div>
    </form>

</div>
@endsection

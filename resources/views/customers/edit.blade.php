@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto py-6 space-y-6">

    {{-- =====================================================
        HEADER / CONTEXT
    ====================================================== --}}
    <div class="bg-white p-4 rounded shadow flex items-center justify-between">
        <div>
            <h1 class="text-xl font-semibold">
                Edit Customer
            </h1>

            <p class="text-sm text-gray-600">
                Account #
                <span class="font-mono font-medium">
                    {{ $customer->account_number }}
                </span>
            </p>
        </div>

        <a href="{{ route('customers.show', $customer) }}"
           class="text-blue-600 hover:underline text-sm">
            ← Back to Customer Profile
        </a>
    </div>

    {{-- =====================================================
        CORE CUSTOMER DETAILS (EDITABLE)
    ====================================================== --}}
    <form method="POST" action="{{ route('customers.update', $customer) }}">
        @csrf
        @method('PUT')

        <div class="bg-white p-6 rounded shadow space-y-6">
            <h2 class="text-lg font-semibold border-b pb-2">
                Customer Details
            </h2>

            @include('customers._form', [
                'customer' => $customer,
                'companies' => $companies,
                'defaultCompanyId' => null,
            ])

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('customers.show', $customer) }}"
                   class="bg-gray-200 px-4 py-2 rounded">
                    Cancel
                </a>

                <button class="bg-blue-600 text-white px-4 py-2 rounded">
                    Save Changes
                </button>
            </div>
        </div>
    </form>

    {{-- =====================================================
        STATUS MANAGEMENT (AUDIT CONTROLLED)
    ====================================================== --}}
    <div class="bg-white p-6 rounded shadow">
        <h2 class="text-lg font-semibold border-b pb-2 mb-4">
            Customer Status
        </h2>

        <p class="mb-3 text-sm">
            Current status:
            <span
                class="
                    inline-block px-2 py-1 text-xs rounded
                    @if ($customer->customer_status === 'active') bg-green-100 text-green-800
                    @elseif($customer->customer_status === 'on_hold') bg-yellow-100 text-yellow-800
                    @else bg-red-100 text-red-800 @endif
                ">
                {{ str_replace('_', ' ', $customer->customer_status) }}
            </span>
        </p>

        <form method="POST" action="{{ route('customers.status.update', $customer) }}">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium mb-1">
                        New status *
                    </label>
                    <select name="new_status" class="border rounded px-3 py-2 w-full">
                        @foreach (['active', 'on_hold', 'blocked'] as $status)
                            <option value="{{ $status }}">
                                {{ ucfirst(str_replace('_', ' ', $status)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">
                        Reason for status change *
                    </label>
                    <input
                        type="text"
                        name="reason"
                        class="border rounded px-3 py-2 w-full"
                        placeholder="Required reason for audit trail"
                        required
                    >
                </div>
            </div>

            <div class="mt-4 flex justify-end">
                <button class="bg-red-600 text-white px-4 py-2 rounded">
                    Change Status
                </button>
            </div>
        </form>
    </div>

</div>
@endsection

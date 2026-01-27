@extends('layouts.app')

@section('content')
    <div class="max-w-3xl mx-auto px-6 py-8">

        {{-- ================= PAGE HEADER ================= --}}
        <div class="mb-8">
            <div class="text-xs uppercase tracking-wider text-gray-500 mb-2">
                Staff / Orders
            </div>

            <h1 class="text-2xl font-semibold text-gray-900">
                Create Order
            </h1>

            <p class="text-sm text-gray-500 mt-1">
                Select a customer to create a new order from their portfolio.
            </p>
        </div>

        {{-- ================= FORM ================= --}}
        <form method="POST" action="{{ route('staff.orders.store') }}">
            @csrf

            <div class="bg-white border border-gray-200 rounded-lg shadow-sm p-6">

                {{-- Customer select --}}
                <div class="mb-6">
                    <label for="customer_id" class="block text-sm font-medium text-gray-700 mb-1">
                        Customer
                    </label>

                    <select id="customer_id" name="customer_id" required
                        class="block w-full rounded-md border border-gray-300 px-3 py-2 text-sm
                           focus:border-blue-500 focus:ring-1 focus:ring-blue-500">
                        <option value="">Select a customer…</option>

                        @foreach ($customers as $customer)
                            <option value="{{ $customer->id }}">
                                {{ $customer->account_number }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-between pt-4 border-t border-gray-200">
                    <a href="{{ route('staff.orders.index') }}" class="crm-btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="crm-btn-primary px-8">
                        Create Order
                    </button>
                </div>

            </div>
        </form>

    </div>
@endsection

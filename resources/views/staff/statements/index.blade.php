@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-8">
        <h1 class="text-2xl font-semibold text-gray-900">Customer Statements</h1>
        <p class="text-sm text-gray-500 mt-1">
            Select a customer to view their statement.
        </p>

        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mt-6 p-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Customer
                    </label>

                    <select
                        class="w-full border rounded px-3 py-2 text-sm"
                        onchange="if (this.value) { window.location.href = '{{ url('/staff/statements') }}/' + this.value; }"
                    >
                        <option value="">Select customer…</option>

                        @foreach ($customers as $c)
                            <option value="{{ $c->id }}">
                                {{ $c->account_number }} — {{ $c->registered_company_name ?: $c->contact_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <a href="{{ route('customers.index') }}"
                       class="crm-btn-secondary w-full inline-block text-center">
                        Back to Customers
                    </a>
                </div>

            </div>
        </div>
    </div>
@endsection

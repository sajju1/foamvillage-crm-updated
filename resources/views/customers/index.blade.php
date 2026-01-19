@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6">

        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
            <h1 class="text-xl font-semibold">Customers</h1>

            <a href="{{ route('customers.create') }}"
               class="bg-blue-600 text-white px-4 py-2 rounded">
                + New Customer
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" class="mb-4 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                <div>
                    <label class="block text-sm font-medium mb-1">Company</label>
                    <select name="company_id" class="border rounded px-3 py-2 w-full">
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}" @selected($companyId == $company->id)>
                                {{ $company->legal_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium mb-1">Status</label>
                    <select name="status" class="border rounded px-3 py-2 w-full">
                        <option value="">All</option>
                        <option value="active" @selected(($status ?? '') === 'active')>Active</option>
                        <option value="on_hold" @selected(($status ?? '') === 'on_hold')>On Hold</option>
                        <option value="blocked" @selected(($status ?? '') === 'blocked')>Blocked</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium mb-1">Search</label>
                    <input
                        type="text"
                        name="q"
                        value="{{ $q ?? '' }}"
                        class="border rounded px-3 py-2 w-full"
                        placeholder="Account #, name, email, phone, VAT, postcode..."
                    >
                </div>

                <div class="flex gap-2">
                    <button class="bg-gray-900 text-white px-4 py-2 rounded">
                        Apply
                    </button>
                    <a href="{{ route('customers.index', ['company_id' => $companyId]) }}"
                       class="bg-gray-200 px-4 py-2 rounded">
                        Reset
                    </a>
                </div>
            </div>
        </form>

        {{-- Customers Table --}}
        <div class="bg-white shadow rounded">
            <table class="w-full text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="p-2 text-left">Account #</th>
                        <th class="p-2 text-left">Customer</th>
                        <th class="p-2 text-left">Customer Reg. No.</th>
                        <th class="p-2 text-left">Company</th>
                        <th class="p-2 text-left">Company Reg. No.</th>
                        <th class="p-2 text-left">Status</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($customers as $customer)
                        <tr
                            class="border-t hover:bg-gray-50 cursor-pointer"
                            onclick="window.location='{{ route('customers.show', $customer) }}'"
                        >

                            {{-- Account number --}}
                            <td class="p-2 font-mono">
                                {{ $customer->account_number }}
                            </td>

                            {{-- Customer name + email --}}
                            <td class="p-2">
                                <div class="font-medium text-blue-700 hover:underline">
                                    {{ $customer->contact_name }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $customer->email }}
                                </div>
                            </td>

                            {{-- Customer registration number --}}
                            <td class="p-2">
                                {{ $customer->customer_registration_number ?? '—' }}
                            </td>

                            {{-- Company name --}}
                            <td class="p-2">
                                {{ $customer->company->legal_name }}
                            </td>

                            {{-- Company registration number --}}
                            <td class="p-2">
                                {{ $customer->company->company_number ?? '—' }}
                            </td>

                            {{-- Status --}}
                            <td class="p-2 capitalize">
                                <span
                                    class="
                                        inline-block px-2 py-1 text-xs rounded
                                        @if ($customer->customer_status === 'active') bg-green-100 text-green-800
                                        @elseif($customer->customer_status === 'on_hold') bg-yellow-100 text-yellow-800
                                        @else bg-red-100 text-red-800 @endif
                                    ">
                                    {{ str_replace('_', ' ', $customer->customer_status) }}
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr class="border-t">
                            <td colspan="6" class="p-4 text-center text-gray-500">
                                No customers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="p-4">
                {{ $customers->links() }}
            </div>
        </div>
    </div>
@endsection

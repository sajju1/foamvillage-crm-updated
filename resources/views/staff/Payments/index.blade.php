@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- ================= HEADER ================= --}}
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Customer Payments</h1>
                <p class="text-sm text-gray-500">
                    One-off and customer-level payments (invoice payments are handled inside invoices).
                </p>
            </div>

            <a href="{{ route('staff.payments.create') }}" class="crm-btn-primary">
                ➕ Add Payment
            </a>
        </div>

        {{-- ================= FLASH ================= --}}
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-green-800">
                {{ session('success') }}
            </div>
        @endif

        {{-- ================= TABLE ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Date</th>
                        <th class="px-4 py-3 text-left">Customer</th>
                        <th class="px-4 py-3 text-right">Amount</th>
                        <th class="px-4 py-3 text-left">Method</th>
                        <th class="px-4 py-3 text-left">Reference</th>
                        <th class="px-4 py-3 text-center">Allocations</th>
                        <th class="px-4 py-3 text-right">Actions</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    @forelse ($payments as $payment)
                        <tr class="hover:bg-gray-50">
                            {{-- Date --}}
                            <td class="px-4 py-3">
                                {{ $payment->paid_at?->format('d M Y') }}
                            </td>

                            {{-- Customer --}}
                            <td class="px-4 py-3">
                                @php
                                    $customer = $payment->allocations->first()?->invoice?->customer;
                                @endphp

                                @if ($customer)
                                    <a href="{{ route('customers.show', $customer->id) }}"
                                        class="text-blue-600 hover:underline">
                                        {{ $customer->account_number ?? '—' }}
                                        –
                                        {{ $customer->registered_company_name ?? $customer->contact_name }}
                                    </a>
                                @else
                                    —
                                @endif
                            </td>

                            {{-- Amount --}}
                            <td class="px-4 py-3 text-right font-medium">
                                £{{ number_format($payment->amount, 2) }}
                            </td>

                            {{-- Method --}}
                            <td class="px-4 py-3">
                                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                            </td>

                            {{-- Reference --}}
                            <td class="px-4 py-3 text-gray-500">
                                {{ $payment->payment_reference ?? '—' }}
                            </td>

                            {{-- Allocations --}}
                            <td class="px-4 py-3 text-center">
                                @if ($payment->allocations->count())
                                    <span
                                        class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                               bg-green-100 text-green-800">
                                        {{ $payment->allocations->count() }}
                                        invoice{{ $payment->allocations->count() > 1 ? 's' : '' }}
                                    </span>
                                @else
                                    <span class="text-gray-400 text-xs">Unallocated</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-4 py-3 text-right">
                                <a href="{{ route('staff.payments.show', $payment) }}"
                                   class="text-blue-600 hover:underline text-sm">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 py-6 text-center text-gray-500">
                                No customer payments recorded yet.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ================= PAGINATION ================= --}}
        <div class="mt-6">
            {{ $payments->links() }}
        </div>

    </div>
@endsection

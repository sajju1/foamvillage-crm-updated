@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-6 py-8">

    {{-- ================= HEADER ================= --}}
    <div class="mb-6">
        <h1 class="text-2xl font-semibold text-gray-900">Payment Details</h1>
        <p class="text-sm text-gray-500">
            View payment information and how it was allocated across invoices.
        </p>
    </div>

    {{-- ================= PAYMENT SUMMARY ================= --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-100">
            <h2 class="font-semibold text-gray-800">Payment</h2>
        </div>

        <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
            <div>
                <span class="text-gray-500">Amount</span><br>
                <span class="text-lg font-semibold">
                    £{{ number_format($payment->amount, 2) }}
                </span>
            </div>

            <div>
                <span class="text-gray-500">Paid At</span><br>
                {{ $payment->paid_at?->format('d M Y') }}
            </div>

            <div>
                <span class="text-gray-500">Method</span><br>
                {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
            </div>

            <div>
                <span class="text-gray-500">Reference</span><br>
                {{ $payment->payment_reference ?? '—' }}
            </div>

            <div class="md:col-span-2">
                <span class="text-gray-500">Notes</span><br>
                {{ $payment->notes ?: '—' }}
            </div>
        </div>
    </div>

    {{-- ================= ALLOCATIONS ================= --}}
    <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
        <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
            <h2 class="font-semibold text-gray-800">Invoice Allocations</h2>

            @php
                $allocatedTotal = $payment->allocations->sum('allocated_amount');
                $remaining = round($payment->amount - $allocatedTotal, 2);
            @endphp

            <div class="text-sm">
                <span class="text-gray-500">Allocated:</span>
                <span class="font-medium">£{{ number_format($allocatedTotal, 2) }}</span>

                <span class="mx-2 text-gray-300">|</span>

                <span class="text-gray-500">Remaining:</span>
                <span class="font-medium {{ $remaining > 0 ? 'text-red-600' : 'text-green-700' }}">
                    £{{ number_format($remaining, 2) }}
                </span>
            </div>
        </div>

        <div class="px-6 py-4">
            @if ($payment->allocations->isEmpty())
                <p class="text-sm text-gray-500">
                    This payment has not been allocated to any invoices.
                </p>
            @else
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                        <tr>
                            <th class="px-3 py-2 text-left">Invoice</th>
                            <th class="px-3 py-2 text-right">Allocated</th>
                            <th class="px-3 py-2 text-right">Invoice Balance (Now)</th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-gray-100">
                        @foreach ($payment->allocations as $allocation)
                            <tr>
                                <td class="px-3 py-2">
                                    <a href="{{ route('staff.invoices.show', $allocation->invoice) }}"
                                       class="text-blue-600 hover:underline">
                                        {{ $allocation->invoice->invoice_number }}
                                    </a>
                                </td>

                                <td class="px-3 py-2 text-right">
                                    £{{ number_format($allocation->allocated_amount, 2) }}
                                </td>

                                <td class="px-3 py-2 text-right">
                                    £{{ number_format($allocation->invoice->balance_due, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>

    {{-- ================= ACTIONS ================= --}}
    <div class="flex justify-end gap-3">
        <a href="{{ route('staff.payments.index') }}" class="crm-btn-secondary">
            Back to Payments
        </a>
    </div>

</div>
@endsection

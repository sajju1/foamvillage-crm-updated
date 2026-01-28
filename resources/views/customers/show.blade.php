@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto py-6 space-y-8">

        {{-- =====================================================
    HEADER
    ====================================================== --}}
        <div class="bg-white shadow rounded px-6 py-5">
            <div class="flex items-center justify-between">
                <div>
                    <div class="text-sm font-medium text-gray-500">
                        Customer Account
                    </div>

                    <div class="text-3xl font-bold font-mono text-gray-900 leading-tight">
                        {{ $customer->account_number }}
                    </div>
                </div>

                <span
                    class="inline-flex items-center px-4 py-1.5 text-sm rounded-full font-semibold
                @if ($customer->customer_status === 'active') bg-green-100 text-green-800
                @elseif($customer->customer_status === 'on_hold') bg-yellow-100 text-yellow-800
                @else bg-red-100 text-red-800 @endif">
                    {{ str_replace('_', ' ', $customer->customer_status) }}
                </span>
            </div>

            <div class="mt-4 border-t"></div>

            <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-1">
                    <div class="text-lg font-semibold text-gray-900">
                        {{ $customer->contact_name }}
                    </div>

                    @if ($customer->registered_company_name)
                        <div class="text-sm text-gray-600">
                            {{ $customer->registered_company_name }}
                        </div>
                    @endif
                </div>

                <div class="space-y-1 text-sm text-gray-700">
                    @if ($customer->customer_registration_number)
                        <div>
                            <span class="font-medium text-gray-500">Reg No:</span>
                            {{ $customer->customer_registration_number }}
                        </div>
                    @endif

                    @if ($customer->vat_number)
                        <div>
                            <span class="font-medium text-gray-500">VAT:</span>
                            {{ $customer->vat_number }}
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="mt-2 text-xs text-gray-500">
            Managed under company:
            <span class="font-medium text-gray-700">
                {{ $customer->company->legal_name }}
            </span>
        </div>

        {{-- =====================================================
    CUSTOMER DETAILS
    ====================================================== --}}
        <div class="bg-white shadow rounded">
            <div class="border-b p-4 flex justify-between items-center">
                <h2 class="font-semibold">Customer Details</h2>

                {{-- If this link "doesn't work", it's almost always route or auth.
                 We'll give you a quick route-list command below. --}}
                <a href="{{ route('customers.edit', $customer) }}" class="text-blue-600 text-sm hover:underline">
                    Edit Customer
                </a>
            </div>

            <div class="p-6 text-sm">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-3">
                        <div><span class="text-gray-500">Email:</span> {{ $customer->email ?? '—' }}</div>
                        <div><span class="text-gray-500">Primary Phone:</span> {{ $customer->primary_phone ?? '—' }}</div>
                        <div><span class="text-gray-500">Secondary Phone:</span> {{ $customer->secondary_phone ?? '—' }}
                        </div>
                    </div>

                    <div class="space-y-3">
                        <div><span class="text-gray-500">VAT:</span> {{ $customer->vat_number ?? '—' }}</div>
                        <div>
                            <span class="text-gray-500">Credit Limit:</span>
                            {{ $customer->credit_limit !== null ? number_format($customer->credit_limit, 2) : '—' }}
                        </div>
                        <div>
                            <span class="text-gray-500">Payment Terms:</span>
                            {{ str_replace('_', ' ', $customer->payment_terms ?? '—') }}
                        </div>
                    </div>
                </div>

                @if ($customer->internal_notes)
                    <div class="mt-6 pt-6 border-t">
                        <div class="text-gray-500 mb-1">Internal Notes</div>
                        <div class="bg-gray-50 border rounded p-3 whitespace-pre-line">
                            {{ $customer->internal_notes }}
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- =====================================================
    ADDRESSES (MODAL-BASED)
    ====================================================== --}}
        @include('customers._addresses')

        {{-- =====================================================
    PRODUCT PORTFOLIO (keep your existing partial)
    ====================================================== --}}
        @php
            $products = $products ?? \App\Models\Product\Product::with('variations')->orderBy('product_name')->get();
            $portfolio = $portfolio ?? collect();
            $activePortfolioMap = $activePortfolioMap ?? collect();
        @endphp

        @include('customers._portfolio')

        {{-- =====================================================
CUSTOMER PAYMENTS
====================================================== --}}
        <div class="bg-white shadow rounded">
            <div class="border-b p-4 flex items-center justify-between">
                <h2 class="font-semibold">Payments</h2>

                <a href="{{ route('staff.customers.payments.create', $customer) }}" class="crm-btn-primary text-sm">
                    ➕ Add Payment
                </a>
            </div>

            <div class="p-6">
                @php
                    $payments = \App\Models\Payment\Payment::query()
                        ->whereNull('invoice_id')
                        ->whereHas('allocations.invoice', function ($q) use ($customer) {
                            $q->where('customer_id', $customer->id);
                        })
                        ->with('allocations.invoice')
                        ->orderByDesc('paid_at')
                        ->limit(5)
                        ->get();
                @endphp

                @if ($payments->isEmpty())
                    <p class="text-sm text-gray-500">
                        No customer payments recorded yet.
                    </p>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="px-3 py-2 text-left">Date</th>
                                    <th class="px-3 py-2 text-right">Amount</th>
                                    <th class="px-3 py-2 text-center">Allocations</th>
                                    <th class="px-3 py-2 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @foreach ($payments as $payment)
                                    <tr>
                                        <td class="px-3 py-2">
                                            {{ $payment->paid_at?->format('d M Y') }}
                                        </td>

                                        <td class="px-3 py-2 text-right font-medium">
                                            £{{ number_format($payment->amount, 2) }}
                                        </td>

                                        <td class="px-3 py-2 text-center">
                                            {{ $payment->allocations->count() }}
                                        </td>

                                        <td class="px-3 py-2 text-right">
                                            <a href="{{ route('staff.payments.show', $payment) }}"
                                                class="text-blue-600 hover:underline text-sm">
                                                View
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-4 text-right">
                        <a href="{{ route('staff.payments.index') }}" class="text-sm text-blue-600 hover:underline">
                            View all payments →
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- =====================================================
FINANCIAL SUMMARY
====================================================== --}}
        <div id="financial-summary" class="bg-white shadow rounded overflow-hidden">

            {{-- ================= HEADER ================= --}}
            <div class="bg-gray-50 border-b px-6 py-4 flex flex-col md:flex-row md:items-center md:justify-between gap-3">

                <div>
                    <h2 class="text-sm font-semibold tracking-wide text-gray-700 uppercase">
                        Financial Summary
                    </h2>
                    <p class="mt-1 text-xs text-gray-500">
                        Live figures derived from invoices and payments
                    </p>
                </div>

                {{-- Date Range Filter --}}
                <form id="financial-summary-form" class="flex items-center gap-2 text-xs">

                    <select name="financial_range" id="financial-range"
                        class="rounded-md border-gray-300 text-xs px-2 py-1">
                        <option value="">All time</option>
                        <option value="3m" @selected(request('financial_range') === '3m')>Last 3 months</option>
                        <option value="6m" @selected(request('financial_range') === '6m')>Last 6 months</option>
                        <option value="12m" @selected(request('financial_range') === '12m')>Last 12 months</option>
                        <option value="custom" @selected(request('financial_range') === 'custom')>Custom</option>
                    </select>

                    {{-- Always rendered, JS controls visibility --}}
                    <input type="date" name="from" id="financial-from" value="{{ request('from') }}"
                        class="rounded-md border-gray-300 text-xs px-2 py-1 hidden">

                    <input type="date" name="to" id="financial-to" value="{{ request('to') }}"
                        class="rounded-md border-gray-300 text-xs px-2 py-1 hidden">
                </form>

            </div>

            {{-- ================= BODY ================= --}}
            <div id="financial-summary-body">

                @php
                    $invoiceQuery = \App\Models\Invoice\Invoice::where('customer_id', $customer->id);

                    $range = request('financial_range');

                    if ($range === '3m') {
                        $invoiceQuery->where('issued_at', '>=', now()->subMonths(3));
                    } elseif ($range === '6m') {
                        $invoiceQuery->where('issued_at', '>=', now()->subMonths(6));
                    } elseif ($range === '12m') {
                        $invoiceQuery->where('issued_at', '>=', now()->subMonths(12));
                    } elseif ($range === 'custom' && request('from') && request('to')) {
                        $invoiceQuery->whereBetween('issued_at', [request('from'), request('to')]);
                    }

                    $invoices = $invoiceQuery->get();

                    $totalInvoiced = $invoices->sum('total_amount');
                    $totalPaid = $invoices->sum(fn($invoice) => $invoice->total_paid);
                    $outstanding = $totalInvoiced - $totalPaid;

                    $today = now()->startOfDay();

                    $overdue = $invoices
                        ->filter(
                            fn($invoice) => $invoice->balance_due > 0 &&
                                $invoice->due_date &&
                                $invoice->due_date->lt($today),
                        )
                        ->sum(fn($invoice) => $invoice->balance_due);
                @endphp

                <div class="px-6 py-6 grid grid-cols-1 md:grid-cols-3 gap-x-8 gap-y-6">

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Total Invoiced</div>
                        <div class="mt-1 text-2xl font-semibold text-gray-900">
                            £{{ number_format($totalInvoiced, 2) }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Total Paid</div>
                        <div class="mt-1 text-2xl font-semibold text-green-700">
                            £{{ number_format($totalPaid, 2) }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Outstanding</div>
                        <div class="mt-1 text-2xl font-semibold {{ $outstanding > 0 ? 'text-red-700' : 'text-gray-900' }}">
                            £{{ number_format($outstanding, 2) }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Overdue</div>
                        <div class="mt-1 text-xl font-semibold {{ $overdue > 0 ? 'text-red-700' : 'text-gray-900' }}">
                            £{{ number_format($overdue, 2) }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Credit Limit</div>
                        <div class="mt-1 text-xl font-medium text-gray-900">
                            {{ $customer->credit_limit !== null ? '£' . number_format($customer->credit_limit, 2) : '—' }}
                        </div>
                    </div>

                    <div>
                        <div class="text-xs uppercase tracking-wide text-gray-500">Payment Terms</div>
                        <div class="mt-1 text-xl font-medium text-gray-900">
                            {{ str_replace('_', ' ', $customer->payment_terms ?? '—') }}
                        </div>
                    </div>

                </div>
            </div>

            {{-- ================= FOOTER ================= --}}
            <div class="border-t bg-gray-50 px-6 py-3 text-xs text-gray-500">
                Values update automatically as invoices and payments change.
                Statements and exports will be introduced in a later module.
            </div>

        </div>





        {{-- =====================================================
    COMMUNICATIONS (PLACEHOLDER)
    ====================================================== --}}
        <div class="bg-white shadow rounded p-6">
            <h2 class="font-semibold mb-1">Communications</h2>
            <p class="text-sm text-gray-500">
                Messages, notes, emails/calls, and customer-visible communications.
            </p>
        </div>

    </div>
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {

                const form = document.getElementById('financial-summary-form');
                let body = document.getElementById('financial-summary-body');

                const rangeSelect = document.getElementById('financial-range');
                const fromInput = document.getElementById('financial-from');
                const toInput = document.getElementById('financial-to');

                if (!form || !body || !rangeSelect) return;

                function toggleCustomDates() {
                    const isCustom = rangeSelect.value === 'custom';
                    fromInput.classList.toggle('hidden', !isCustom);
                    toInput.classList.toggle('hidden', !isCustom);
                }

                async function refreshSummary() {

                    const params = new URLSearchParams(new FormData(form)).toString();
                    const url = `${window.location.pathname}?${params}#financial-summary`;

                    // Keep URL in sync
                    history.replaceState(null, '', url);

                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    const html = await response.text();
                    const temp = document.createElement('div');
                    temp.innerHTML = html;

                    const newBody = temp.querySelector('#financial-summary-body');

                    if (newBody) {
                        body.replaceWith(newBody);
                        body = newBody; // 🔥 CRITICAL FIX
                    }
                }

                // Initial state
                toggleCustomDates();

                // Events
                rangeSelect.addEventListener('change', () => {
                    toggleCustomDates();
                    refreshSummary();
                });

                fromInput.addEventListener('change', refreshSummary);
                toInput.addEventListener('change', refreshSummary);
            });
        </script>
    @endpush



@endsection

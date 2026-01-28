@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- ================= HEADER ================= --}}
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-4">
                <div>
                    <h1 class="text-2xl font-semibold text-gray-900">
                        Invoices
                    </h1>
                    <p class="text-sm text-gray-500 mt-1">
                        All issued invoices
                    </p>
                </div>

                @if ($overdueCount > 0)
                    <a href="{{ route('staff.invoices.index', ['status' => 'overdue']) }}"
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                          bg-red-100 text-red-700 hover:bg-red-200">
                        {{ $overdueCount }} Overdue
                    </a>
                @endif

                @if ($dueSoonCount > 0)
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm font-semibold
                             bg-yellow-100 text-yellow-800">
                        {{ $dueSoonCount }} Due Soon
                    </span>
                @endif
            </div>

            @if ($overdueCount > 0)
                <form method="POST" action="{{ route('staff.invoices.send-overdue-reminders') }}"
                    onsubmit="return confirm('Send payment reminders for all overdue invoices?')">
                    @csrf
                    <button class="crm-btn-secondary">
                        🔔 Email All Overdue
                    </button>
                </form>
            @endif
        </div>

        {{-- ================= APPLY CREDIT CONTEXT ================= --}}
        @if (!empty($applyCreditNote))
            <div class="mb-6 p-4 rounded-lg bg-blue-50 border border-blue-200">
                <div class="flex flex-wrap items-center justify-between gap-4">
                    <div>
                        <p class="text-sm text-blue-900 font-semibold">
                            Applying Credit Note: {{ $applyCreditNote->credit_note_number }}
                        </p>
                        <p class="text-xs text-blue-700 mt-1">
                            Remaining credit:
                            £{{ number_format($applyCreditNote->remaining_amount, 2) }}
                        </p>
                    </div>

                    <a href="{{ route('staff.credit-notes.show', $applyCreditNote) }}"
                        class="text-sm font-medium text-blue-700 hover:underline">
                        View Credit Note →
                    </a>
                </div>
            </div>
        @endif

        {{-- ================= SEARCH & FILTER ================= --}}
        <form method="GET" class="mb-4 flex flex-wrap gap-3 items-center">
            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="Search invoice, customer, account number..."
                class="border border-gray-300 rounded px-3 py-2 text-sm w-80">

            <select name="status" class="border rounded px-3 py-2 text-sm">
                <option value="">All</option>
                <option value="due" {{ request('status') === 'due' ? 'selected' : '' }}>Due</option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>Overdue</option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>Paid</option>
            </select>

            <button class="crm-btn-primary">
                🔍 Apply
            </button>

            @if (request('q') || request('status'))
                <a href="{{ route('staff.invoices.index') }}" class="crm-btn-secondary">
                    Reset
                </a>
            @endif
        </form>

        {{-- ================= TABLE ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full border-collapse">

                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="w-8 px-2"></th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Invoice No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Account</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Customer</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Invoice Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">Due Date</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Balance</th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">Status</th>
                    </tr>
                </thead>

                @forelse ($invoices as $invoice)
                    @php
                        $customer = $invoice->customer;
                        $customerName = $customer->registered_company_name ?: $customer->contact_name;
                    @endphp

                    <tbody x-data="{ open: false }" class="divide-y divide-gray-200">

                        {{-- COLLAPSED ROW --}}
                        <tr class="hover:bg-gray-50">
                            <td class="px-2 text-center">
                                <button @click="open = !open" class="text-gray-400 hover:text-gray-700 font-bold text-lg">
                                    <span x-show="!open">+</span>
                                    <span x-show="open">−</span>
                                </button>
                            </td>

                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                {{ $invoice->invoice_number }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $customer->account_number }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-900">
                                {{ $customerName }}
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700">
                                {{ $invoice->issued_at?->format('d M Y') }}
                            </td>

                            <td class="px-4 py-3 text-sm">
                                @if ($invoice->balance_due <= 0)
                                    <span class="text-gray-500">
                                        {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                                    </span>
                                @elseif ($invoice->due_date && $invoice->due_date->isPast())
                                    <span class="text-red-700 font-semibold">
                                        {{ $invoice->due_date->format('d M Y') }}
                                    </span>
                                @else
                                    <span class="text-green-700 font-semibold">
                                        {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-right font-semibold">
                                £{{ number_format($invoice->balance_due, 2) }}
                            </td>

                            <td class="px-4 py-3 text-sm text-right">
                                @if ($invoice->balance_due <= 0)
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">PAID</span>
                                @elseif ($invoice->total_paid > 0)
                                    <span class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs">PART-PAID</span>
                                @elseif ($invoice->due_date && $invoice->due_date->isPast())
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">OVERDUE</span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">DUE</span>
                                @endif
                            </td>
                        </tr>

                        {{-- EXPANDED ROW --}}
                        <tr x-show="open" x-cloak class="bg-gray-50">
                            <td colspan="8" class="px-8 py-6">
                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                                    {{-- AMOUNTS --}}
                                    <div class="text-sm space-y-2">
                                        <div class="text-gray-600">
                                            Total
                                            <div class="font-semibold text-gray-900">
                                                £{{ number_format($invoice->total_amount, 2) }}
                                            </div>
                                        </div>

                                        <div class="text-gray-600">
                                            Paid
                                            <div class="font-semibold text-green-700">
                                                £{{ number_format($invoice->total_paid, 2) }}
                                            </div>
                                        </div>

                                        <div class="text-gray-600">
                                            Balance
                                            <div class="font-semibold text-red-700">
                                                £{{ number_format($invoice->balance_due, 2) }}
                                            </div>
                                        </div>
                                    </div>

                                    {{-- REMINDER --}}
                                    <div class="text-sm space-y-2">
                                        @if ($invoice->balance_due > 0 && $invoice->due_date)
                                            <form method="POST"
                                                action="{{ route('staff.invoices.send-reminder', $invoice) }}"
                                                onsubmit="return confirm('Send payment reminder for this invoice?')">
                                                @csrf
                                                <button class="text-amber-700 hover:underline font-medium">
                                                    🔔 Send Reminder
                                                </button>
                                            </form>
                                        @endif

                                        @if ($invoice->last_reminded_at)
                                            <div class="text-xs text-gray-400">
                                                Last reminded {{ $invoice->last_reminded_at->diffForHumans() }}
                                            </div>
                                        @endif
                                    </div>

                                    {{-- ACTIONS --}}
                                    <div class="flex flex-col gap-2 text-sm">
                                        <a href="{{ route('staff.invoices.show', $invoice) }}"
                                            class="text-blue-600 hover:underline">
                                            View Invoice
                                        </a>

                                        <a href="{{ route('staff.invoices.print', $invoice) }}" target="_blank"
                                            class="text-gray-600 hover:underline">
                                            Print
                                        </a>

                                        <a href="{{ route('staff.invoices.pdf', $invoice) }}"
                                            class="text-gray-600 hover:underline">
                                            PDF
                                        </a>

                                        @if (!empty($applyCreditNote) && $invoice->balance_due > 0)
                                            <button type="button"
                                                @click="$dispatch('open-apply-credit', {
            invoiceId: {{ $invoice->id }},
            invoiceNumber: '{{ $invoice->invoice_number }}'
        })"
                                                class="text-emerald-700 font-medium hover:underline text-left">
                                                ➕ Apply Credit
                                            </button>
                                        @endif


                                        <form action="{{ route('staff.invoices.email', $invoice) }}" method="POST">
                                            @csrf
                                            <button type="submit" onclick="return confirm('Send this invoice by email?')"
                                                class="text-emerald-600 hover:underline text-left">
                                                Email
                                            </button>
                                        </form>
                                    </div>

                                </div>
                            </td>
                        </tr>
                        {{-- Apply Credit Modal (per invoice) --}}
                        @if (!empty($applyCreditNote) && $invoice->balance_due > 0)
                            @include('staff.invoices.partials.apply-credit', compact('invoice'))
                        @endif
                    </tbody>
                @empty
                    <tbody>
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                No invoices found.
                            </td>
                        </tr>
                    </tbody>
                @endforelse

            </table>
        </div>

        <div class="mt-6">
            {{ $invoices->links() }}
        </div>

    </div>
@endsection

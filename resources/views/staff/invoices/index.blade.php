@extends('layouts.app')

@section('content')
    <div class="max-w-7xl mx-auto px-6 py-8">

        {{-- ================= HEADER ================= --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
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

        {{-- ================= SEARCH & FILTER ================= --}}
        <form method="GET" class="mb-4 flex flex-wrap gap-3 items-center">

            <input type="text" name="q" value="{{ request('q') }}"
                placeholder="Search invoice, customer, account number..."
                class="border border-gray-300 rounded px-3 py-2 text-sm w-80">

            <select name="status" class="border rounded px-3 py-2 text-sm">
                <option value="">All</option>
                <option value="due" {{ request('status') === 'due' ? 'selected' : '' }}>
                    Due
                </option>
                <option value="overdue" {{ request('status') === 'overdue' ? 'selected' : '' }}>
                    Overdue
                </option>
                <option value="paid" {{ request('status') === 'paid' ? 'selected' : '' }}>
                    Paid
                </option>
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
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Invoice No
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Account
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Customer
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Invoice Date
                        </th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Due Date
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                            Amount
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                            Status
                        </th>
                        <th class="px-4 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                            Actions
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @forelse ($invoices as $invoice)
                        @php
                            $customer = $invoice->customer;
                            $customerName = $customer->registered_company_name ?: $customer->contact_name;
                        @endphp

                        <tr class="hover:bg-gray-50">
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
                                £{{ number_format($invoice->total_amount, 2) }}
                            </td>

                            <td class="px-4 py-3 text-sm text-right">
                                @if ($invoice->balance_due <= 0)
                                    <span class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs">
                                        PAID
                                    </span>
                                @elseif ($invoice->due_date && $invoice->due_date->isPast())
                                    <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs">
                                        OVERDUE
                                    </span>
                                @else
                                    <span class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs">
                                        DUE
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-sm text-right space-x-3 whitespace-nowrap">
                                @if ($invoice->balance_due > 0 && $invoice->due_date)
                                    <form method="POST" action="{{ route('staff.invoices.send-reminder', $invoice) }}"
                                        class="inline" onsubmit="return confirm('Send payment reminder for this invoice?')">
                                        @csrf
                                        <button type="submit" class="text-amber-600 hover:underline">
                                            🔔 Reminder
                                        </button>
                                    </form>
                                @endif
                                @if ($invoice->last_reminded_at)
                                    <span class="text-xs text-gray-400">
                                        Reminded {{ $invoice->last_reminded_at->diffForHumans() }}
                                    </span>
                                @endif

                                <a href="{{ route('staff.invoices.show', $invoice) }}"
                                    class="text-blue-600 hover:underline">
                                    View
                                </a>

                                <a href="{{ route('staff.invoices.print', $invoice) }}" target="_blank"
                                    class="text-gray-600 hover:underline">
                                    Print
                                </a>

                                <a href="{{ route('staff.invoices.pdf', $invoice) }}"
                                    class="text-gray-600 hover:underline">
                                    PDF
                                </a>

                                <form action="{{ route('staff.invoices.email', $invoice) }}" method="POST" class="inline">
                                    @csrf
                                    <button type="submit" class="text-emerald-600 hover:underline"
                                        onclick="return confirm('Send this invoice by email?')">
                                        Email
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                No invoices found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>
        </div>

        <div class="mt-6">
            {{ $invoices->links() }}
        </div>

    </div>
@endsection

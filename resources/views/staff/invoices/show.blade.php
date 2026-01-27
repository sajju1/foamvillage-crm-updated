@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- ================= HEADER / ACTIONS ================= --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">Invoice</h1>
                <p class="text-sm text-gray-500">{{ $invoice->invoice_number }}</p>
            </div>

            <div class="flex gap-2 print:hidden items-center">
                <a href="{{ route('staff.invoices.print', $invoice) }}" target="_blank" class="crm-btn-secondary">
                    🖨 Print
                </a>

                <a href="{{ route('staff.invoices.pdf', $invoice) }}" class="crm-btn-secondary">
                    📄 PDF
                </a>

                <form method="POST" action="{{ route('staff.invoices.email', $invoice) }}" class="inline">
                    @csrf
                    <button type="submit" class="crm-btn-secondary"
                        onclick="return confirm('Send this invoice by email?')">
                        ✉️ Email
                    </button>
                </form>

                @if ($invoice->balance_due > 0)
                    <a href="{{ route('staff.invoices.edit', $invoice) }}" class="crm-btn-primary">
                        ✏️ Edit Invoice
                    </a>
                @endif


                <button type="button" onclick="openPaymentsModal()" class="crm-btn-secondary">
                    💳 Payments
                </button>

                <span
                    class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                {{ $invoice->balance_due <= 0
                    ? 'bg-green-100 text-green-800'
                    : ($invoice->total_paid > 0
                        ? 'bg-yellow-100 text-yellow-800'
                        : 'bg-gray-100 text-gray-800') }}">
                    {{ $invoice->balance_due <= 0 ? 'Paid' : ($invoice->total_paid > 0 ? 'Partially Paid' : 'Unpaid') }}
                </span>
            </div>
        </div>
        {{-- ================= INVOICE META ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">

                <div>
                    <div class="text-gray-500">Due Date</div>

                    @if ($invoice->balance_due <= 0)
                        <div class="font-semibold text-gray-500">
                            {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                        </div>
                    @elseif ($invoice->due_date && $invoice->due_date->isPast())
                        <div class="font-semibold text-red-700">
                            {{ $invoice->due_date->format('d M Y') }}
                        </div>
                    @else
                        <div class="font-semibold text-green-700">
                            {{ $invoice->due_date?->format('d M Y') ?? '—' }}
                        </div>
                    @endif
                </div>


                <div>
                    <div class="text-gray-500">Issued Date</div>
                    <div class="font-semibold">
                        {{ $invoice->issued_at->format('d M Y') }}
                    </div>
                </div>

            </div>
        </div>


        {{-- ================= INVOICE LINES (VIEW ONLY) ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6 overflow-x-auto">
            <table class="min-w-full border-collapse text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-3 text-left">Description</th>
                        <th class="px-4 py-3 text-right">Qty</th>
                        <th class="px-4 py-3 text-right">Unit (ex VAT)</th>
                        <th class="px-4 py-3 text-right">VAT %</th>
                        <th class="px-4 py-3 text-right">VAT</th>
                        <th class="px-4 py-3 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach ($invoice->lines as $line)
                        <tr>
                            <td class="px-4 py-3 align-top">
                                <div class="font-medium text-gray-900">
                                    {{ $line->description }}
                                </div>

                                @if ($line->note)
                                    <div class="mt-1 text-sm text-gray-600">
                                        {{ $line->note }}
                                    </div>
                                @endif
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ number_format($line->quantity, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                £{{ number_format($line->unit_price_ex_vat, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right">
                                {{ number_format($line->vat_rate, 2) }}%
                            </td>

                            <td class="px-4 py-3 text-right">
                                £{{ number_format($line->vat_amount, 2) }}
                            </td>

                            <td class="px-4 py-3 text-right font-medium">
                                £{{ number_format($line->line_total_inc_vat, 2) }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- ================= TOTALS & PAYMENT SUMMARY ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm">
            <table class="min-w-full border-collapse text-sm">
                <tbody class="divide-y divide-gray-200">
                    <tr>
                        <td class="px-6 py-4 text-gray-600">Invoice Total</td>
                        <td class="px-6 py-4 text-right font-semibold">
                            £{{ number_format($invoice->total_amount, 2) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="px-6 py-4 text-gray-600">Total Paid</td>
                        <td class="px-6 py-4 text-right text-green-700 font-medium">
                            £{{ number_format($invoice->total_paid, 2) }}
                        </td>
                    </tr>

                    <tr>
                        <td class="px-6 py-4 text-gray-900 font-semibold">Balance Due</td>
                        <td class="px-6 py-4 text-right font-semibold text-red-600">
                            £{{ number_format($invoice->balance_due, 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <script>
        function openPaymentsModal() {
            document.getElementById('payments-modal').classList.remove('hidden');
            document.getElementById('payments-modal').classList.add('flex');
        }

        function closePaymentsModal() {
            document.getElementById('payments-modal').classList.add('hidden');
            document.getElementById('payments-modal').classList.remove('flex');
        }
    </script>

    @include('staff.invoices.partials.payments-modal')
@endsection

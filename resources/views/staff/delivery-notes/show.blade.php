@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- ================= HEADER ================= --}}
        <div class="flex items-start justify-between mb-6">
            <div>
                <h1 class="text-2xl font-semibold text-gray-900">
                    {{ strtoupper($deliveryNote->type) }} Note
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $deliveryNote->delivery_note_number }}
                </p>
            </div>

            {{-- ACTION BUTTONS --}}
            <div class="flex gap-2 print:hidden">
                <a href="{{ route('staff.delivery-notes.print', $deliveryNote) }}" target="_blank" class="crm-btn-secondary">
                    🖨 Print
                </a>

                <a href="{{ route('staff.delivery-notes.pdf', $deliveryNote) }}" class="crm-btn-secondary">
                    📄 PDF
                </a>

                {{-- EMAIL DELIVERY NOTE --}}
                <form method="POST" action="{{ route('staff.delivery-notes.email', $deliveryNote) }}" class="inline">
                    @csrf
                    <button type="submit" class="crm-btn-secondary"
                        onclick="return confirm('Send this delivery note by email?')">
                        ✉️ Email Delivery Note
                    </button>
                </form>

                {{-- CONVERT TO INVOICE --}}
                @if (!$deliveryNote->invoice_id)
                    <button type="button" class="crm-btn-primary"
                        onclick="document.getElementById('convertToInvoicePanel').classList.toggle('hidden')">
                        🧾 Convert to Invoice
                    </button>
                @else
                    <span class="crm-btn-secondary opacity-60 cursor-not-allowed">
                        🧾 Already Invoiced
                    </span>
                @endif
            </div>
        </div>

        {{-- ================= CONVERT TO INVOICE PANEL ================= --}}
        @if (!$deliveryNote->invoice_id)
            <div id="convertToInvoicePanel" class="hidden bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
                <form method="POST" action="{{ route('staff.delivery-notes.convert-to-invoice', $deliveryNote) }}"
                    class="px-6 py-4 space-y-4">
                    @csrf

                    <h2 class="text-sm font-semibold text-gray-700">
                        Invoice Payment Terms
                    </h2>

                    {{-- PAYMENT TERMS --}}
                    <div>
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Payment terms
                        </label>
                        <select id="payment_terms" name="payment_terms" class="w-full border-gray-300 rounded-md text-sm"
                            required onchange="toggleCustomDueDate(this.value)">
                            <option value="">Select payment terms</option>
                            <option value="due_on_receipt">Due on receipt</option>
                            <option value="7_days">7 days</option>
                            <option value="14_days">14 days</option>
                            <option value="30_days">30 days</option>
                            <option value="custom">Custom date</option>
                        </select>

                    </div>

                    {{-- CUSTOM DUE DATE --}}
                    <div id="customDueDateWrapper" class="hidden">
                        <label class="block text-xs font-medium text-gray-600 mb-1">
                            Custom due date
                        </label>
                        <input type="date" id="custom_due_date" name="custom_due_date"
                            class="border-gray-300 rounded-md text-sm">
                    </div>


                    {{-- ACTIONS --}}
                    <div class="flex items-center gap-3 pt-2">
                        <button type="submit" class="crm-btn-primary"
                            onclick="return confirm('Convert this delivery note into an invoice?')">
                            Create Invoice
                        </button>

                        <button type="button" class="text-sm text-gray-500"
                            onclick="document.getElementById('convertToInvoicePanel').classList.add('hidden')">
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        @endif

        {{-- ================= CUSTOMER / ORDER ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
            <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">

                <div>
                    <div class="text-gray-500">Customer</div>
                    <div class="font-semibold">
                        {{ $deliveryNote->customer->customer_name }}
                    </div>
                    <div class="text-xs text-gray-500">
                        Account: {{ $deliveryNote->customer->account_number }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Order</div>
                    <div class="font-semibold">
                        {{ $deliveryNote->order->order_number }}
                    </div>
                </div>

                <div>
                    <div class="text-gray-500">Issued At</div>
                    <div class="font-semibold">
                        {{ $deliveryNote->issued_at?->format('d M Y H:i') }}
                    </div>
                </div>

            </div>
        </div>

        {{-- ================= ADDRESS ================= --}}
        @if ($deliveryNote->address)
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 text-sm">
                    <div class="text-gray-500 mb-1">Delivery Address</div>
                    <div class="font-semibold leading-relaxed">
                        {{ $deliveryNote->address->address_line1 }}<br>
                        @if ($deliveryNote->address->address_line2)
                            {{ $deliveryNote->address->address_line2 }}<br>
                        @endif
                        @if ($deliveryNote->address->address_line3)
                            {{ $deliveryNote->address->address_line3 }}<br>
                        @endif
                        {{ $deliveryNote->address->city }}<br>
                        {{ $deliveryNote->address->postcode }}<br>
                        {{ $deliveryNote->address->country }}
                    </div>
                </div>
            </div>
        @endif

        {{-- ================= ITEMS ================= --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-sm overflow-x-auto">
            <table class="min-w-full border-collapse">

                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-600 uppercase">
                            Product / Variation
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                            Requested
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-600 uppercase">
                            Processed
                        </th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-200">
                    @foreach ($deliveryNote->lines as $line)
                        <tr>
                            <td class="px-6 py-3 text-sm">
                                {{ $line->orderLine->product->product_name ?? 'Product' }}
                                <div class="text-xs text-gray-500">
                                    {{ $line->orderLine->productVariation?->display_name ?? '—' }}
                                </div>
                            </td>

                            <td class="px-6 py-3 text-right text-sm font-semibold">
                                {{ $line->orderLine->requested_quantity }}
                            </td>

                            <td class="px-6 py-3 text-right text-sm font-semibold">
                                {{ $line->processed_quantity }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>

            </table>
        </div>

    </div>
    <script>
        function toggleCustomDueDate(value) {
            const wrapper = document.getElementById('customDueDateWrapper');
            const input = document.getElementById('custom_due_date');

            if (value === 'custom') {
                wrapper.classList.remove('hidden');
            } else {
                wrapper.classList.add('hidden');
                if (input) {
                    input.value = '';
                }
            }
        }
    </script>

@endsection

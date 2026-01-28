@extends('layouts.app')

@section('content')
    <div class="max-w-6xl mx-auto px-6 py-8">

        {{-- ================= HEADER ================= --}}
        <div class="mb-6">
            <h1 class="text-2xl font-semibold text-gray-900">Add Customer Payment</h1>
            <p class="text-sm text-gray-500">
                Record a customer payment and allocate it to one or more invoices.
            </p>
        </div>

        {{-- ================= ERRORS ================= --}}
        @if ($errors->any())
            <div class="mb-6 rounded-md bg-red-50 p-4 text-red-800">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('staff.payments.store') }}">
            @csrf

            {{-- ================= CUSTOMER ================= --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Customer</h2>
                </div>

                <div class="px-6 py-4">
                    @if ($customer)
                        <input type="hidden" name="customer_id" value="{{ $customer->id }}">

                        <div class="text-gray-900 font-medium">
                            {{ $customer->account_number ?? '—' }}
                            –
                            {{ $customer->registered_company_name ?? $customer->contact_name }}
                        </div>

                        <div class="text-sm text-gray-500">
                            {{ $customer->is_account_customer ? 'Account customer' : 'Non-account customer' }}
                        </div>
                    @else
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Select Customer
                        </label>

                        <select name="customer_id" required class="w-full rounded-md border-gray-300"
                            onchange="if (this.value) window.location.href = '{{ url('/staff/customers') }}/' + this.value + '/payments/create';">
                            <option value="">— Select customer —</option>

                            @foreach ($customers as $c)
                                @php
                                    $accountNumber = $c->account_number ?: '—';
                                    $displayName = $c->registered_company_name ?: $c->contact_name;
                                @endphp

                                <option value="{{ $c->id }}">
                                    {{ $accountNumber }} – {{ $displayName }}
                                </option>
                            @endforeach
                        </select>
                    @endif
                </div>
            </div>

            {{-- ================= PAYMENT DETAILS ================= --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Payment Details</h2>
                </div>

                <div class="px-6 py-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Amount
                        </label>
                        <input type="number" name="amount" id="payment-amount" step="0.01" min="0.01" required
                            value="{{ old('amount') }}" class="w-full rounded-md border-gray-300">

                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Payment Method
                        </label>

                        <select name="payment_method" required class="w-full rounded-md border-gray-300">
                            <option value="">— Select method —</option>

                            <option value="bank_transfer" @selected(old('payment_method') === 'bank_transfer')>
                                Bank Transfer
                            </option>
                            <option value="cash" @selected(old('payment_method') === 'cash')>
                                Cash
                            </option>
                            <option value="card" @selected(old('payment_method') === 'card')>
                                Card
                            </option>
                            <option value="cheque" @selected(old('payment_method') === 'cheque')>
                                Cheque
                            </option>
                        </select>
                    </div>


                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Reference
                        </label>
                        <input type="text" name="payment_reference" value="{{ old('payment_reference') }}"
                            class="w-full rounded-md border-gray-300">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Paid At
                        </label>
                        <input type="date" name="paid_at" required value="{{ old('paid_at', now()->toDateString()) }}"
                            class="w-full rounded-md border-gray-300">
                    </div>

                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Notes
                        </label>
                        <textarea name="notes" rows="3" class="w-full rounded-md border-gray-300">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- ================= ALLOCATIONS ================= --}}
            <div class="bg-white border border-gray-200 rounded-lg shadow-sm mb-6">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h2 class="font-semibold text-gray-800">Invoice Allocations</h2>
                    <p class="text-sm text-gray-500">
                        Allocate the payment across one or more open invoices.
                    </p>
                </div>

                <div class="px-6 py-4">
                    @if ($invoices->isEmpty())
                        <p class="text-sm text-gray-500">
                            Select a customer to view outstanding invoices.
                        </p>
                    @else
                        <div class="mb-4 flex gap-6 text-sm">
                            <div>
                                <span class="text-gray-500">Allocated:</span>
                                <span class="font-medium">
                                    £<span id="allocated-total">0.00</span>
                                </span>
                            </div>

                            <div>
                                <span class="text-gray-500">Remaining:</span>
                                <span class="font-medium">
                                    £<span id="remaining-amount">0.00</span>
                                </span>
                            </div>
                        </div>

                        <table class="w-full text-sm">
                            <thead class="bg-gray-50 text-gray-600 uppercase text-xs">
                                <tr>
                                    <th class="px-3 py-2 text-left">Invoice</th>
                                    <th class="px-3 py-2 text-right">Balance Due</th>
                                    <th class="px-3 py-2 text-right">Allocate</th>
                                </tr>
                            </thead>

                            <tbody class="divide-y divide-gray-100">
                                @foreach ($invoices as $invoice)
                                    <tr>
                                        <td class="px-3 py-2">
                                            {{ $invoice->invoice_number }}
                                        </td>

                                        <td class="px-3 py-2 text-right">
                                            £{{ number_format($invoice->balance_due, 2) }}
                                        </td>

                                        <td class="px-3 py-2 text-right">
                                            <div class="flex items-center justify-end gap-2">
                                                <input type="number" name="allocations[{{ $invoice->id }}]"
                                                    step="0.01" min="0" max="{{ $invoice->balance_due }}"
                                                    value="0"
                                                    class="w-28 rounded-md border-gray-300 text-right allocation-input">

                                                <button type="button" class="text-xs text-blue-600 hover:underline"
                                                    onclick="
                                                        this.previousElementSibling.value = '{{ $invoice->balance_due }}';
                                                        this.previousElementSibling.dispatchEvent(new Event('input'));
                                                    ">
                                                    Max
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @endif
                </div>
            </div>

            {{-- ================= ACTIONS ================= --}}
            <div class="flex items-center justify-end gap-3">
                <a href="{{ route('staff.payments.index') }}" class="crm-btn-secondary">
                    Cancel
                </a>

                <button type="submit" id="save-payment-btn" class="crm-btn-primary opacity-50 cursor-not-allowed" disabled>
                    Save Payment
                </button>

            </div>

        </form>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {

                const paymentAmountInput = document.querySelector('input[name="amount"]');
                const allocationInputs = document.querySelectorAll('input[name^="allocations["]');
                const allocatedEl = document.getElementById('allocated-total');
                const remainingEl = document.getElementById('remaining-amount');
                document.getElementById('payment-amount')?.focus();


                function recalc() {
                    let allocated = 0;
                    allocationInputs.forEach(input => {
                        allocated += parseFloat(input.value || 0);
                    });

                    const paymentAmount = parseFloat(paymentAmountInput.value || 0);
                    const remaining = paymentAmount - allocated;

                    allocatedEl.textContent = allocated.toFixed(2);
                    remainingEl.textContent = remaining.toFixed(2);

                    const saveBtn = document.getElementById('save-payment-btn');

                    if (remaining === 0 && paymentAmount > 0) {
                        remainingEl.classList.remove('text-red-600');
                        saveBtn.disabled = false;
                        saveBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                    } else {
                        if (remaining < 0) {
                            remainingEl.classList.add('text-red-600');
                        }
                        saveBtn.disabled = true;
                        saveBtn.classList.add('opacity-50', 'cursor-not-allowed');
                    }
                }


                paymentAmountInput?.addEventListener('input', recalc);
                allocationInputs.forEach(input => input.addEventListener('input', recalc));

                recalc();
            });
        </script>
    @endpush

@endsection

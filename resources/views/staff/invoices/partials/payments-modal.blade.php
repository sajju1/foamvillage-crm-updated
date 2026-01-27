<div id="payments-modal"
     class="fixed inset-0 bg-black bg-opacity-40 hidden items-center justify-center z-50">

    <div class="bg-white rounded-lg shadow-lg w-full max-w-3xl p-6">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold">
                Payments – {{ $invoice->invoice_number }}
            </h2>
            <button onclick="closePaymentsModal()"
                    class="text-gray-500 hover:text-gray-700 text-xl">
                &times;
            </button>
        </div>

        {{-- Existing payments --}}
        <div class="mb-6">
            <h3 class="text-sm font-semibold text-gray-700 mb-2">
                Payment History
            </h3>

            @if ($invoice->payments->count())
                <table class="min-w-full text-sm border border-gray-200 rounded">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-3 py-2 text-left">Date</th>
                            <th class="px-3 py-2 text-left">Method</th>
                            <th class="px-3 py-2 text-left">Reference</th>
                            <th class="px-3 py-2 text-right">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y">
                        @foreach ($invoice->payments as $payment)
                            <tr>
                                <td class="px-3 py-2">
                                    {{ $payment->paid_at->format('d M Y') }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ $payment->payment_reference ?? '—' }}
                                </td>
                                <td class="px-3 py-2 text-right font-medium">
                                    £{{ number_format($payment->amount, 2) }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <p class="text-sm text-gray-500">
                    No payments recorded for this invoice.
                </p>
            @endif
        </div>

        {{-- Add payment --}}
        <div class="border-t pt-4">
            <h3 class="text-sm font-semibold text-gray-700 mb-3">
                Record Payment
            </h3>

            <form method="POST"
                  action="{{ route('staff.invoices.payments.store', $invoice) }}">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs text-gray-500 mb-1">
                            Amount (max £{{ number_format($invoice->balance_due, 2) }})
                        </label>
                        <input type="number"
                               name="amount"
                               step="0.01"
                               max="{{ $invoice->balance_due }}"
                               required
                               class="w-full border rounded px-2 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">
                            Payment Date
                        </label>
                        <input type="date"
                               name="paid_at"
                               value="{{ now()->toDateString() }}"
                               required
                               class="w-full border rounded px-2 py-2 text-sm">
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">
                            Payment Method
                        </label>
                        <select name="payment_method"
                                required
                                class="w-full border rounded px-2 py-2 text-sm">
                            <option value="">Select method</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="card">Card</option>
                            <option value="cash">Cash</option>
                            <option value="cheque">Cheque</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs text-gray-500 mb-1">
                            Reference
                        </label>
                        <input type="text"
                               name="payment_reference"
                               class="w-full border rounded px-2 py-2 text-sm">
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-xs text-gray-500 mb-1">
                        Notes (optional)
                    </label>
                    <textarea name="notes"
                              rows="2"
                              class="w-full border rounded px-2 py-2 text-sm"></textarea>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button"
                            onclick="closePaymentsModal()"
                            class="crm-btn-secondary">
                        Cancel
                    </button>
                    <button type="submit"
                            class="crm-btn-primary">
                        Save Payment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function openPaymentsModal() {
        const modal = document.getElementById('payments-modal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
    }

    function closePaymentsModal() {
        const modal = document.getElementById('payments-modal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
    }
</script>

@php
    $availableCredits = $invoice->customer
        ->creditNotes()
        ->get()
        ->filter(fn ($credit) => $credit->remaining_amount > 0);
@endphp

@if ($invoice->balance_due > 0 && $availableCredits->isNotEmpty())
<div
    x-data="{ open: false }"
    x-on:open-apply-credit.window="open = true"
>
    {{-- Modal Backdrop --}}
    <div
        x-show="open"
        x-cloak
        class="fixed inset-0 bg-black/50 flex items-center justify-center z-50"
    >
        {{-- Modal Panel --}}
        <div class="bg-white border border-gray-200 rounded-lg shadow-lg p-6 w-full max-w-4xl">

            {{-- Header --}}
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900">
                    Apply Credit
                </h3>

                <button
                    type="button"
                    @click="open = false"
                    class="text-gray-500 hover:text-gray-800 text-xl font-bold"
                >
                    ×
                </button>
            </div>

            {{-- Form --}}
            <form method="POST"
                  action="{{ route('staff.invoices.apply-credit', $invoice) }}"
                  class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                @csrf

                {{-- Credit Note --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Credit Note
                    </label>
                    <select name="credit_note_id" required
                            class="w-full border rounded px-3 py-2 text-sm">
                        <option value="">Select credit note</option>
                        @foreach ($availableCredits as $credit)
                            <option value="{{ $credit->id }}">
                                {{ $credit->credit_note_number }}
                                — £{{ number_format($credit->remaining_amount, 2) }} available
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Amount --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Amount to apply
                    </label>
                    <input type="number"
                           name="amount"
                           step="0.01"
                           min="0.01"
                           max="{{ $invoice->balance_due }}"
                           required
                           class="w-full border rounded px-3 py-2 text-sm"
                           placeholder="e.g. 25.00">
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Notes (optional)
                    </label>
                    <input type="text"
                           name="notes"
                           maxlength="255"
                           class="w-full border rounded px-3 py-2 text-sm"
                           placeholder="Reason / reference">
                </div>

                {{-- Submit --}}
                <div>
                    <button type="submit"
                            class="crm-btn-primary w-full"
                            onclick="return confirm('Apply this credit to the invoice?')">
                        Apply Credit
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

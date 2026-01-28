<?php

namespace App\Http\Controllers\Staff\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice\Invoice;
use App\Models\Finance\CreditNote;
use App\Models\Finance\CreditAllocation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplyCreditController extends Controller
{
    /**
     * Apply a credit note to an invoice.
     */
    public function store(Request $request, Invoice $invoice)
    {
        $data = $request->validate([
            'credit_note_id' => ['required', 'exists:credit_notes,id'],
            'amount'         => ['required', 'numeric', 'min:0.01'],
            'notes'          => ['nullable', 'string', 'max:255'],
        ]);

        $creditNote = CreditNote::where('id', $data['credit_note_id'])
            ->where('customer_id', $invoice->customer_id)
            ->firstOrFail();

        DB::transaction(function () use ($invoice, $creditNote, $data) {

            // Lock rows for financial safety
            $invoice->lockForUpdate();
            $creditNote->lockForUpdate();

            $remainingInvoiceBalance = $invoice->balance_due;
            $remainingCreditBalance  = $creditNote->remaining_amount;

            $amountToApply = min(
                $data['amount'],
                $remainingInvoiceBalance,
                $remainingCreditBalance
            );

            if ($amountToApply <= 0) {
                abort(422, 'No available balance to apply.');
            }

            CreditAllocation::create([
                'credit_note_id' => $creditNote->id,
                'invoice_id'     => $invoice->id,
                'amount_applied' => $amountToApply,
                'applied_at'     => now(),
                'notes'          => $data['notes'] ?? null,
            ]);
        });

        return redirect()
            ->route('staff.invoices.show', $invoice)
            ->with('success', 'Credit applied successfully.');
    }
}

<?php

namespace App\Http\Controllers\Staff\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice\Invoice;
use App\Models\Payment\Payment;
use Illuminate\Http\Request;

class InvoicePaymentController extends Controller
{
    /**
     * Store a payment against a single invoice.
     * Invoice-level payments are capped at invoice balance.
     */
    public function store(Request $request, Invoice $invoice)
    {
        $invoice->load('payments');

        $balanceDue = $invoice->balance_due;

        if ($balanceDue <= 0) {
            return back()->withErrors('This invoice is already fully paid.');
        }

        $data = $request->validate([
            'amount'            => ['required', 'numeric', 'min:0.01'],
            'payment_method'    => ['required', 'string', 'max:50'],
            'payment_reference' => ['nullable', 'string', 'max:100'],
            'paid_at'           => ['required', 'date'],
            'notes'             => ['nullable', 'string', 'max:500'],
        ]);

        if ($data['amount'] > $balanceDue) {
            return back()->withErrors(
                'Payment amount cannot exceed the invoice balance. Use customer payment instead.'
            );
        }

        Payment::create([
            'invoice_id'        => $invoice->id,
            'amount'            => $data['amount'],
            'currency'          => $invoice->currency ?? 'GBP',
            'payment_method'    => $data['payment_method'],
            'payment_reference' => $data['payment_reference'] ?? null,
            'paid_at'           => $data['paid_at'],
            'notes'             => $data['notes'] ?? null,
        ]);

        return back()->with('success', 'Payment recorded successfully.');
    }
}

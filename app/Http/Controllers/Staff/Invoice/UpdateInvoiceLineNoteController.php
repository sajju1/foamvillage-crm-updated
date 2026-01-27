<?php

namespace App\Http\Controllers\Staff\Invoice;

use App\Http\Controllers\Controller;
use App\Models\Invoice\InvoiceLine;
use Illuminate\Http\Request;

class UpdateInvoiceLineNoteController extends Controller
{
    public function update(Request $request, InvoiceLine $line)
    {
        $invoice = $line->invoice;

        if ($invoice->balance_due <= 0) {
            abort(403, 'This invoice is fully paid and cannot be modified.');
        }

        $validated = $request->validate([
            'note' => ['nullable', 'string', 'max:500'],
        ]);

        $line->update([
            'note' => $validated['note'],
        ]);

        return back()->with('success', 'Invoice line note updated.');
    }
}

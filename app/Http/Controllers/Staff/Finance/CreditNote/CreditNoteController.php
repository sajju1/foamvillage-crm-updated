<?php

namespace App\Http\Controllers\Staff\Finance\CreditNote;

use App\Http\Controllers\Controller;
use App\Models\Finance\CreditNote;
use Illuminate\Http\Request;

class CreditNoteController extends Controller
{
    /**
     * Display a listing of credit notes.
     */
    public function index(Request $request)
    {
        $creditNotes = CreditNote::with(['customer', 'allocations'])
            ->orderByDesc('issued_at')
            ->paginate(20);

        return view('staff.finance.credit-notes.index', compact('creditNotes'));
    }

    /**
     * Display a single credit note.
     */
    public function show(\App\Models\Finance\CreditNote $creditNote)
    {
        $creditNote->load([
            'customer',
            'allocations.invoice',
        ]);

        return view('staff.finance.credit-notes.show', compact('creditNote'));
    }
}

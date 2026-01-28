<?php

namespace App\Http\Controllers\Staff\Finance\CreditNote;

use App\Http\Controllers\Controller;
use App\Models\Finance\CreditNote;
use App\Models\Customer\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;

class CreateCreditNoteController extends Controller
{
    /**
     * Show create credit note form.
     */
    public function create()
    {
        $customers = Customer::orderBy('registered_company_name')
            ->orderBy('contact_name')
            ->get();

        return view('staff.finance.credit-notes.create', compact('customers'));
    }

    /**
     * Store credit note.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id'  => ['required', 'exists:customers,id'],
            'issued_at'    => ['required', 'date'],
            'total_amount' => ['required', 'numeric', 'min:0.01'],
            'reason'       => ['nullable', 'string', 'max:255'],
        ]);

        CreditNote::create([
            'credit_note_number' => 'CN-' . now()->format('ymd') . '-' . strtoupper(Str::random(6)),
            'customer_id'        => $data['customer_id'],
            'issued_at'          => Carbon::parse($data['issued_at']),
            'reason'             => $data['reason'],
            'total_amount'       => $data['total_amount'],
            'currency'           => 'GBP',
        ]);

        return redirect()
            ->route('staff.credit-notes.index')
            ->with('success', 'Credit note created successfully.');
    }
}

<?php

namespace App\Http\Controllers\Staff\Payment;

use App\Http\Controllers\Controller;
use App\Models\Payment\Payment;

class PaymentHistoryController extends Controller
{
    public function show(Payment $payment)
    {
        $payment->load([
            'allocations.invoice'
        ]);

        return view('staff.payments.show', compact('payment'));
    }
}

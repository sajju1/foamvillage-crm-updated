<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use App\Models\Invoice\Invoice;

class PaymentAllocation extends Model
{
    protected $table = 'payment_allocations';

    protected $fillable = [
        'payment_id',
        'invoice_id',
        'allocated_amount',
    ];

    public function payment()
    {
        return $this->belongsTo(Payment::class);
    }

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

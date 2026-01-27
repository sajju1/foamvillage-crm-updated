<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Invoice\Invoice;

class Payment extends Model
{
    protected $fillable = [
        'invoice_id',
        'amount',
        'currency',
        'payment_method',
        'payment_reference',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'amount'  => 'decimal:2',
        'paid_at'=> 'datetime',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}

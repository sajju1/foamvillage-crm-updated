<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Invoice\Invoice;

class CreditAllocation extends Model
{
    protected $fillable = [
        'credit_note_id',
        'invoice_id',
        'amount_applied',
        'applied_at',
        'notes',
    ];

    protected $casts = [
        'amount_applied' => 'decimal:2',
        'applied_at'     => 'datetime',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function creditNote(): BelongsTo
    {
        return $this->belongsTo(CreditNote::class);
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}

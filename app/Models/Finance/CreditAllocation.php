<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Invoice\Invoice;

class CreditAllocation extends Model
{
    protected $table = 'credit_allocations';

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

    /* -----------------------------------------------------------------
     | Relationships
     |------------------------------------------------------------------*/

    public function creditNote(): BelongsTo
    {
        return $this->belongsTo(CreditNote::class, 'credit_note_id');
    }

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }
}

<?php

namespace App\Models\Payment;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'paid_at' => 'datetime',
    ];

    /* ================= RELATIONSHIPS ================= */

    /**
     * Legacy: single-invoice payment (existing data)
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * New: allocation-based payments (future-safe)
     */
    public function allocations(): HasMany
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    /* ================= ACCESSORS ================= */

    /**
     * Total allocated amount across invoices
     */
    public function getAllocatedTotalAttribute(): float
    {
        return (float) $this->allocations()->sum('allocated_amount');
    }

    /**
     * Remaining unallocated amount
     */
    public function getUnallocatedAmountAttribute(): float
    {
        return (float) ($this->amount - $this->allocated_total);
    }
}

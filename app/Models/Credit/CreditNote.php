<?php

namespace App\Models\Credit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Customer;

class CreditNote extends Model
{
    protected $fillable = [
        'credit_note_number',
        'customer_id',
        'issued_at',
        'reason',
        'total_amount',
        'currency',
    ];

    protected $casts = [
        'issued_at'    => 'datetime',
        'total_amount'=> 'decimal:2',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(CreditAllocation::class);
    }

    /* ================= DERIVED FINANCIALS ================= */

    public function getAllocatedAmountAttribute(): float
    {
        return (float) $this->allocations->sum('amount_applied');
    }

    public function getRemainingBalanceAttribute(): float
    {
        return (float) ($this->total_amount - $this->allocated_amount);
    }

    public function getIsFullyUsedAttribute(): bool
    {
        return $this->remaining_balance <= 0;
    }
}

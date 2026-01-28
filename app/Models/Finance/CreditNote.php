<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Casts\Attribute;
use App\Models\Customer\Customer;
use App\Models\Invoice\Invoice;

class CreditNote extends Model
{
    protected $table = 'credit_notes';

    protected $fillable = [
        'credit_note_number',
        'customer_id',
        'issued_at',
        'reason',
        'total_amount',
        'currency',
    ];

    protected $casts = [
        'issued_at'   => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    /* -----------------------------------------------------------------
     | Relationships
     |------------------------------------------------------------------*/

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(CreditAllocation::class, 'credit_note_id');
    }

    /**
     * Invoices this credit has been applied to (through allocations)
     */
    public function invoices()
    {
        return $this->belongsToMany(
            Invoice::class,
            'credit_allocations',
            'credit_note_id',
            'invoice_id'
        )->withPivot([
            'amount_applied',
            'applied_at',
            'notes',
        ]);
    }

    /* -----------------------------------------------------------------
     | Derived Attributes (DO NOT STORE)
     |------------------------------------------------------------------*/

    /**
     * Total amount already allocated from this credit
     */
    protected function totalAllocated(): Attribute
    {
        return Attribute::get(function () {
            return $this->allocations()->sum('amount_applied');
        });
    }

    /**
     * Remaining credit available for allocation
     */
    protected function remainingAmount(): Attribute
    {
        return Attribute::get(function () {
            return bcsub(
                (string) $this->total_amount,
                (string) $this->total_allocated,
                2
            );
        });
    }

    /* -----------------------------------------------------------------
     | Helpers
     |------------------------------------------------------------------*/

    public function hasRemainingBalance(): bool
    {
        return $this->remaining_amount > 0;
    }
}

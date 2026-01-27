<?php

namespace App\Models\Orders;

use App\Models\Company\Company;
use App\Models\Customer\Customer;
use App\Models\User;
use App\Models\Delivery\DeliveryNote;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'customer_id',
        'order_number',
        'source_channel',
        'status',
        'submitted_at',
        'acknowledged_at',
        'internal_notes',
        'created_by',
    ];

    protected $casts = [
        'submitted_at'    => 'datetime',
        'acknowledged_at' => 'datetime',
    ];

    /**
     * Relationships
     */

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class);
    }
public function deliveryNotes()
{
    return $this->hasMany(DeliveryNote::class);
}
    /**
     * State helpers (read-only)
     */

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isSubmitted(): bool
    {
        return $this->status === 'submitted';
    }

    public function isEditableByCustomer(): bool
    {
        return $this->isDraft() && $this->source_channel === 'customer_portal';
    }

    public function isLockedForCustomer(): bool
    {
        return $this->submitted_at !== null;
    }

    public function canBeAcknowledged(): bool
    {
        return $this->status === 'submitted';
    }
    public function orderLines()
    {
        return $this->hasMany(
            \App\Models\Orders\OrderLine::class,
            'order_id'
        );
    }
    public function assertIsEditable(): void
{
    if (! $this->isDraft()) {
        abort(403, 'This order is locked and cannot be modified.');
    }
}

}

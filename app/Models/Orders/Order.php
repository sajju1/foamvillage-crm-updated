<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use App\Models\Customer\Customer;

class Order extends Model
{
    protected $table = 'orders';

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

    /* =========================
     | Relationships
     |=========================*/

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function lines(): HasMany
    {
        return $this->hasMany(OrderLine::class, 'order_id');
    }

    /* =========================
     | Helpers
     |=========================*/

    public static function generateOrderNumber(): string
    {
        return 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }

    public function isDraft(): bool
    {
        return $this->status === 'draft';
    }

    public function isEditable(): bool
    {
        return $this->isDraft();
    }
}

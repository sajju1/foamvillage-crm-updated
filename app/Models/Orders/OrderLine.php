<?php

namespace App\Models\Orders;

use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variation_id',
        'requested_quantity',
        'processed_quantity',
        'unit_price_ex_vat',
        'vat_rate',
        'line_status',
        'notes',
    ];

    protected $casts = [
        'unit_price_ex_vat' => 'decimal:2',
        'vat_rate' => 'decimal:2',
    ];

    /**
     * Relationships
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class);
    }

    /**
     * Derived quantities
     */
    public function getPendingQuantityAttribute(): int
    {
        return max(
            0,
            $this->requested_quantity - $this->processed_quantity
        );
    }

    /**
     * Pricing helpers
     */
    public function getLineSubtotalAttribute(): float
    {
        return (float) $this->requested_quantity * (float) $this->unit_price_ex_vat;
    }

    /**
     * State helpers
     */
    public function isPending(): bool
    {
        return $this->line_status === 'pending';
    }

    public function isFulfilled(): bool
    {
        return $this->line_status === 'fulfilled';
    }

    public function isCancelled(): bool
    {
        return $this->line_status === 'cancelled';
    }
}

<?php

namespace App\Models\Orders;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;

class OrderLine extends Model
{
    protected $table = 'order_lines';

    protected $fillable = [
        'order_id',
        'product_id',
        'product_variation_id',
        'requested_quantity',
        'processed_quantity',
        'line_status',
        'notes',
    ];

    protected $casts = [
        'requested_quantity' => 'integer',
        'processed_quantity' => 'integer',
    ];

    /* =========================
     | Relationships
     |=========================*/

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    /* =========================
     | Helpers
     |=========================*/

    public function pendingQuantity(): int
    {
        return max(
            0,
            (int) $this->requested_quantity - (int) $this->processed_quantity
        );
    }
}

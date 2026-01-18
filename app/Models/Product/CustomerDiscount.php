<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerDiscount extends Model
{
    protected $table = 'customer_discounts';

    protected $fillable = [
        'customer_id',     // integer only for now (FK added later)
        'product_id',      // nullable (all products)
        'variation_id',    // nullable
        'discount_type',   // fixed | percentage
        'discount_value',
        'valid_from',
        'valid_to',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'product_id' => 'integer',
        'variation_id' => 'integer',
        'discount_value' => 'decimal:2',
        'valid_from' => 'date',
        'valid_to' => 'date',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }
}

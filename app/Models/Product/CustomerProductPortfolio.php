<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CustomerProductPortfolio extends Model
{
    protected $table = 'customer_product_portfolio';

    protected $fillable = [
        'customer_id',   // integer only for now (FK added later)
        'product_id',
        'variation_id',
        'agreed_price',
        'agreed_cost',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected $casts = [
        'customer_id' => 'integer',
        'product_id' => 'integer',
        'variation_id' => 'integer',
        'agreed_price' => 'decimal:2',
        'agreed_cost' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }

    public function getStandardPriceDisplayAttribute(): string
    {
        return $this->standard_price !== null
            ? number_format($this->standard_price, 2)
            : '—';
    }

    public function getCustomerPriceDisplayAttribute(): string
    {
        return $this->agreed_price !== null
            ? number_format($this->agreed_price, 2)
            : '—';
    }
}

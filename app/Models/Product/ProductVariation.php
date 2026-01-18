<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariation extends Model
{
    protected $table = 'product_variations';

    protected $fillable = [
        'product_id',
        'length',
        'width',
        'thickness',
        'size_unit',        // inch | cm
        'colour',
        'variation_code',
        'standard_price',
        'standard_cost',
        'status',           // active | inactive
    ];

    protected $casts = [
        'product_id' => 'integer',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'thickness' => 'decimal:2',
        'standard_price' => 'decimal:2',
        'standard_cost' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }
}

<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FoamPricingRule extends Model
{
    protected $table = 'foam_pricing_rules';

    protected $fillable = [
        'product_id',
        'foam_type',
        'density',
        'price_unit',
        'cost_unit',
        'calculation_formula',
        'status', // active | inactive
    ];

    protected $casts = [
        'product_id' => 'integer',
        'price_unit' => 'decimal:2',
        'cost_unit' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function foamType(): BelongsTo
    {
        return $this->belongsTo(FoamType::class, 'foam_type_id');
    }
}

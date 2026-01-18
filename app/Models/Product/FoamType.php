<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FoamType extends Model
{
    protected $table = 'foam_types';

    protected $fillable = [
        'name',
        'calculation_method',
        'default_price_unit',
        'default_cost_unit',
        'status',
    ];

    protected $casts = [
        'default_price_unit' => 'decimal:4',
        'default_cost_unit' => 'decimal:4',
    ];

    public function foamPricingRules(): HasMany
    {
        return $this->hasMany(FoamPricingRule::class, 'foam_type_id');
    }
}

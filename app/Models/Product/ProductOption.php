<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;

class ProductOption extends Model
{
    protected $table = 'product_options';

    protected $fillable = [
        'option_name',
        'price_modifier_type',   // fixed_add | fixed_deduct | percentage
        'price_modifier_value',
        'cost_modifier_value',
        'status',                // active | inactive
    ];

    protected $casts = [
        'price_modifier_value' => 'decimal:2',
        'cost_modifier_value' => 'decimal:2',
    ];
}

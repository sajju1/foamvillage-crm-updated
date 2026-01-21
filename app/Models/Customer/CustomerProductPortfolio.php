<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;

class CustomerProductPortfolio extends Model
{
    protected $table = 'customer_product_portfolio';

    protected $fillable = [
        'customer_id',
        'product_id',
        'product_variation_id',
        'sellable_label',
        'pricing_type',
        'agreed_price',
        'formula_pricing_mode',
        'rate_override',
        'percentage_modifier',
        'minimum_charge',
        'rounding_rule',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variation()
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }
}

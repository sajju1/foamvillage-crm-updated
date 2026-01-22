<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    
    public function offers()
    {
        return $this->hasMany(CustomerPortfolioOffer::class);
    }
    public function portfolio()
    {
        return $this->hasMany(CustomerProductPortfolio::class);
    }
    public function variation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'variation_id');
    }
    public function productVariation(): BelongsTo
{
    return $this->belongsTo(ProductVariation::class, 'product_variation_id');
}

}

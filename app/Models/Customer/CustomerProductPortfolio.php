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

    /**
     * Base product
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Canonical variation relationship (new)
     */
    public function productVariation(): BelongsTo
    {
        return $this->belongsTo(ProductVariation::class, 'product_variation_id');
    }

    /**
     * BACKWARD-COMPATIBILITY alias
     * Required by existing portfolio module
     */
    public function variation(): BelongsTo
    {
        return $this->productVariation();
    }

    /**
     * Offers linked to this portfolio entry
     */
    public function offers()
    {
        return $this->hasMany(CustomerPortfolioOffer::class);
    }
    
}

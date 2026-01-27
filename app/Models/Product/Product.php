<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company\Company;
use App\Models\Vat\VatRule;


class Product extends Model
{
    protected $fillable = [
        'company_id',
        'category_id',

        'product_name',
        'product_type',
        'manufacturing_type',
        'status',
        'description',

        // Simple product pricing
        'simple_price',
        'simple_cost',
        'vat_rule_id',

    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    public function variations()
    {
        return $this->hasMany(ProductVariation::class);
    }

    public function vatRule()
{
    return $this->belongsTo(VatRule::class);
}

public function effectiveVatRule()
{
    return $this->vatRule ?: VatRule::default();
}

    public function getDisplayNameAttribute(): string
{
    // If a proper name exists, use it
    if (!empty($this->name)) {
        return $this->name;
    }

    // Fallback for rule-based / generated products
    $parts = [];

    if (!empty($this->foam_type)) {
        $parts[] = ucfirst($this->foam_type);
    }

    if (!empty($this->density)) {
        $parts[] = $this->density . ' Density';
    }

    if (!empty($this->thickness)) {
        $parts[] = $this->thickness . ' Inch';
    }

    if (!empty($parts)) {
        return implode(' – ', $parts);
    }

    return 'Product #' . $this->id;
}

}

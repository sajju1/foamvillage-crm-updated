<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company\Company;

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
}

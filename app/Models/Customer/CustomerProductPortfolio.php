<?php

namespace App\Models\Customer;

use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerProductPortfolio extends Model
{
    use HasFactory;

    protected $table = 'customer_product_portfolio';

    protected $fillable = [
        'customer_id',
        'product_id',
        'product_variation_id',
        'agreed_price',
        'effective_from',
        'effective_to',
        'is_active',
    ];

    protected $casts = [
        'agreed_price'   => 'decimal:4',
        'effective_from' => 'date',
        'effective_to'   => 'date',
        'is_active'      => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function product()
    {
        return $this->belongsTo(\App\Models\Product\Product::class, 'product_id');
    }

    public function variation()
    {
        return $this->belongsTo(\App\Models\Product\ProductVariation::class, 'product_variation_id');
    }
}

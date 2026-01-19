<?php

namespace App\Models\Customer;

use App\Models\User;
use App\Models\Product\Product;
use App\Models\Product\ProductVariation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CustomerPortfolioOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'product_id',
        'product_variation_id',
        'offer_type',
        'offer_value',
        'valid_from',
        'valid_to',
        'created_by',
    ];

    protected $casts = [
        'offer_value' => 'decimal:4',
        'valid_from'  => 'date',
        'valid_to'    => 'date',
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
        return $this->belongsTo(Product::class);
    }

    public function productVariation()
    {
        return $this->belongsTo(ProductVariation::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class CustomerPortfolioOffer extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_product_portfolio_id',
        'offer_type',
        'fixed_price',
        'percentage',
        'discount_amount',
        'minimum_quantity',
        'effective_from',
        'effective_to',
        'is_active',
        'created_by',
        'notes',
    ];

    protected $casts = [
        'fixed_price'      => 'decimal:2',
        'percentage'       => 'decimal:2',
        'discount_amount'  => 'decimal:2',
        'minimum_quantity' => 'integer',
        'effective_from'   => 'datetime',
        'effective_to'     => 'datetime',
        'is_active'        => 'boolean',
        'created_by'       => 'integer',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function portfolioEntry(): BelongsTo
    {
        return $this->belongsTo(
            CustomerProductPortfolio::class,
            'customer_product_portfolio_id'
        );
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

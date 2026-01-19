<?php

namespace App\Models\Customer;

use App\Models\Company\Company;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Customer\CustomerProductPortfolio;


class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id',
        'account_number',
        'contact_name',
        'email',
        'primary_phone',
        'secondary_phone',
        'registered_company_name',
        'vat_number',
        'customer_status',
        'credit_limit',
        'payment_terms',
        'internal_notes',
        'account_manager_user_id',
        'customer_registration_number',

    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
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

    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function statusHistory()
    {
        return $this->hasMany(CustomerStatusHistory::class);
    }

    public function productPortfolio()
    {
        return $this->hasMany(CustomerProductPortfolio::class);
    }

    public function portfolioOffers()
    {
        return $this->hasMany(CustomerPortfolioOffer::class);
    }

    public function accountManager()
    {
        return $this->belongsTo(User::class, 'account_manager_user_id');
    }
    public function portfolio()
{
    return $this->hasMany(CustomerProductPortfolio::class);
}
}

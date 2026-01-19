<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class CustomerAddress extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'customer_id',
        'address_type',
        'address_line1',
        'address_line2',
        'address_line3',
        'city',
        'state_region',
        'postcode',
        'country',
        'is_default',
        'is_active',
        'deactivated_at',
    ];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
}

<?php

namespace App\Models\Delivery;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\Orders\Order;
use App\Models\Customer\Customer;
use App\Models\Customer\CustomerAddress;
use App\Models\Invoice\Invoice;
use App\Models\Product\Product;


class DeliveryNote extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'delivery_note_number',
        'type',
        'delivery_address_id',
        'status',
        'issued_at',
        'created_by',
            'invoice_id',

    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function address()
    {
        return $this->belongsTo(CustomerAddress::class, 'delivery_address_id');
    }

    public function lines()
    {
        return $this->hasMany(DeliveryNoteLine::class);
    }
    public function invoice()
{
    return $this->belongsTo(\App\Models\Invoice\Invoice::class);
}
public function product()
{
    return $this->belongsTo(Product::class);
}

}

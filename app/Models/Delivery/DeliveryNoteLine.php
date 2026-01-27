<?php

namespace App\Models\Delivery;

use Illuminate\Database\Eloquent\Model;
use App\Models\Product\Product;
use App\Models\Orders\OrderLine;



class DeliveryNoteLine extends Model
{
    protected $fillable = [
        'delivery_note_id',
        'product_id',
        'quantity',
        'order_line_id',
            'processed_quantity',


    ];

    public function deliveryNote()
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    /**
     * Product linked to this delivery line
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    public function orderLine()
{
    return $this->belongsTo(OrderLine::class);
}
}

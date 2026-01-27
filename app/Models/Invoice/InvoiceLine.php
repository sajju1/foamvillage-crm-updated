<?php

namespace App\Models\Invoice;

use Illuminate\Database\Eloquent\Model;

class InvoiceLine extends Model
{
    protected $fillable = [
        'invoice_id',
        'description',
        'quantity',
        'unit_price_ex_vat',
        'vat_rate',
        'vat_amount',
        'line_total_inc_vat',
        'note',
        'source',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'unit_price_ex_vat' => 'decimal:2',
        'vat_rate' => 'decimal:2',
        'vat_amount' => 'decimal:2',
        'line_total_inc_vat' => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }
}

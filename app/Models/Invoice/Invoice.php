<?php

namespace App\Models\Invoice;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Customer\Customer;
use App\Models\Delivery\DeliveryNote;
use App\Models\Payment\Payment;
use App\Models\Finance\CreditAllocation;
use Illuminate\Support\Carbon;
use App\Models\Payment\PaymentAllocation;




class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'customer_id',
        'delivery_note_id',
        'issued_at',
        'due_date',
        'last_reminded_at', // ✅ ADD
        'subtotal',
        'vat_amount',
        'total_amount',
        'currency',
    ];


    protected $casts = [
        'issued_at'     => 'datetime',
        'due_date'      => 'date',
        'subtotal'      => 'decimal:2',
        'vat_amount'    => 'decimal:2',
        'total_amount'  => 'decimal:2',
    ];
    public function getLastRemindedAtAttribute($value)
    {
        return $value ? Carbon::parse($value) : null;
    }


    /* ================= RELATIONSHIPS ================= */

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function deliveryNote(): BelongsTo
    {
        return $this->belongsTo(DeliveryNote::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function creditAllocations(): HasMany
    {
        return $this->hasMany(CreditAllocation::class);
    }

    public function lines(): HasMany
    {
        return $this->hasMany(InvoiceLine::class);
    }
    public function paymentAllocations()
    {
        return $this->hasMany(PaymentAllocation::class);
    }

    /* ================= DERIVED FINANCIALS ================= */

    public function getTotalPaidAttribute(): float
    {
        $invoicePayments = $this->payments->sum('amount');

        $customerAllocations = $this->paymentAllocations->sum('allocated_amount');

        $credits = $this->creditAllocations->sum('amount_applied');

        return (float) ($invoicePayments + $customerAllocations + $credits);
    }


    public function getBalanceDueAttribute(): float
    {
        $paid = (float) $this->total_paid;
        $credits = (float) $this->total_credits_applied;

        $balance = $this->total_amount - ($paid + $credits);

        return max(0, (float) $balance);
    }


    public function getStatusAttribute(): string
    {
        if ($this->balance_due <= 0) {
            return 'paid';
        }

        if ($this->total_paid > 0 || $this->total_credits_applied > 0) {
            return 'partially_paid';
        }

        return 'issued';
    }

    public function getTotalCreditsAppliedAttribute(): float
    {
        return (float) $this->creditAllocations()->sum('amount_applied');
    }

    /**
     * Recalculate invoice totals from lines.
     * Does NOT persist — caller decides when to save.
     */
    public function refreshTotals(): void
    {
        $subtotal = $this->lines->sum(function ($line) {
            return (float) $line->quantity * (float) $line->unit_price_ex_vat;
        });

        $vatTotal = (float) $this->lines->sum('vat_amount');
        $grandTotal = (float) $this->lines->sum('line_total_inc_vat');

        $this->subtotal     = round($subtotal, 2);
        $this->vat_amount   = round($vatTotal, 2);
        $this->total_amount = round($grandTotal, 2);
    }
}

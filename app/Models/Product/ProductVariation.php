<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductVariation extends Model
{
    protected $table = 'product_variations';

    protected $fillable = [
        'product_id',
        'length',
        'width',
        'thickness',
        'size_unit',        // inch | cm
        'colour',
        'variation_code',
        'standard_price',
        'standard_cost',
        'status',           // active | inactive
    ];

    protected $casts = [
        'product_id' => 'integer',
        'length' => 'decimal:2',
        'width' => 'decimal:2',
        'thickness' => 'decimal:2',
        'standard_price' => 'decimal:2',
        'standard_cost' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function getDisplayNameAttribute(): string
    {
        $format = function ($value): string {
            // Convert things like "90.00" -> "90", "90.50" -> "90.5"
            $value = (string) $value;
            $value = rtrim($value, '0');
            $value = rtrim($value, '.');
            return $value;
        };

        $parts = [];

        if ($this->length !== null && $this->width !== null) {
            $parts[] = $format($this->length) . ' x ' . $format($this->width);
        }

        if ($this->thickness !== null) {
            $parts[] = $format($this->thickness);
        }

        return implode(' x ', $parts);
    }

    public function getFormattedSizeAttribute(): string
    {
        $parts = [];

        if ($this->length) {
            $parts[] = rtrim(rtrim(number_format($this->length, 2), '0'), '.');
        }

        if ($this->width) {
            $parts[] = rtrim(rtrim(number_format($this->width, 2), '0'), '.');
        }

        if ($this->thickness) {
            $parts[] = rtrim(rtrim(number_format($this->thickness, 2), '0'), '.');
        }

        return implode(' x ', $parts);
    }
}

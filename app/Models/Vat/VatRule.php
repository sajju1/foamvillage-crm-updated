<?php

namespace App\Models\Vat;

use Illuminate\Database\Eloquent\Model;

class VatRule extends Model
{
    protected $fillable = [
        'name',
        'rate',
        'is_default',
        'is_active',
    ];

    protected $casts = [
        'is_default' => 'boolean',
        'is_active'  => 'boolean',
        'rate'       => 'decimal:2',
    ];

    public static function default()
{
    return static::where('is_default', true)
        ->where('is_active', true)
        ->first();
}

}

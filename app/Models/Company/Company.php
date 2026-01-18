<?php

namespace App\Models\Company;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    protected $fillable = [
        'legal_name',
        'company_number',
        'vat_number',

        'address_line1',
        'address_line2',
        'address_line3',
        'city',
        'state_region',
        'country',
        'postcode',

        'email',
        'phone',
        'website',

        'logo_file_id',

        'bank_account_name',
        'bank_name',
        'bank_sort_code',
        'bank_account_number',
        'bank_iban',
        'bank_swift_bic',

        'is_default',
        'status',
    ];

    /*
    |--------------------------------------------------------------------------
    | Relationships
    |--------------------------------------------------------------------------
    */

    public function logo()
    {
        return $this->belongsTo(File::class, 'logo_file_id');
    }

    public function brands()
    {
        return $this->hasMany(CompanyBrand::class);
    }

    public function documentDefaults()
    {
        return $this->hasMany(DocumentDefault::class);
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /*
    |--------------------------------------------------------------------------
    | Helpers
    |--------------------------------------------------------------------------
    */

    public static function getDefault()
    {
        return static::where('is_default', true)->first();
    }
}

<?php

namespace App\Models\Company;

use App\Models\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyBrand extends Model
{
    use HasFactory;

    protected $table = 'company_brands';

    protected $fillable = [
        'company_id',
        'brand_name',
        'logo_file_id',
        'brand_email',
        'brand_phone',
        'brand_website',
        'is_default_brand',
        'status',
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

    public function logo()
    {
        return $this->belongsTo(File::class, 'logo_file_id');
    }

    /*
    |--------------------------------------------------------------------------
    | Scopes
    |--------------------------------------------------------------------------
    */

    public function scopeDefault($query)
    {
        return $query->where('is_default_brand', true);
    }
}

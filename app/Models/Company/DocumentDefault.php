<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DocumentDefault extends Model
{
    use HasFactory;

    protected $table = 'document_defaults';

    protected $fillable = [
        'company_id',
        'document_type',

        'header_title_source',
        'header_custom_text',
        'header_logo_source',

        'footer_text_source',
        'footer_custom_text',
        'legal_disclosure_text',

        'show_address',
        'show_company_number',
        'show_vat_number',
        'show_bank_details',
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
}

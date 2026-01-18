<?php

namespace App\Models\Product;

use Illuminate\Database\Eloquent\Model;
use App\Models\Company\Company;

class ProductCategory extends Model
{
    protected $fillable = [
        'company_id',
        'name',
        'status',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}

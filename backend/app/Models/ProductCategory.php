<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductCategory extends Model
{
    use HasFactory;

    protected $table = 'product_categories';

    protected $fillable = [
        'name',
        'description',
        'icon',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    // relasi ke products
    public function products()
    {
        return $this->hasMany(Product::class, 'category_id');
    }
}

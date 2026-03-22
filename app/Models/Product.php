<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes; //Laravel otomatis membuat: deleted_at TIMESTAMP NULL

    protected $table = "products";

    protected $primaryKey = "id";

    protected $fillable = [
        'seller_id',
        'category_id',
        'title',
        'description',
        'stock',
        'price',
        'rating',
        'download_count',
        'file_path',
        'thumbnail',
        'status',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $casts = [
        'price' => 'integer',
        'stock' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // relasi ke category
    public function category()
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    // relasi ke user (seller)
    public function seller()
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    protected $appends = [
        'rating_class'
    ];

    public function getRatingClassAttribute(): string
    {
        $ratingClass = "";
        if ($this->rating >= 8.5) {
            $ratingClass = "Top Rated";
        } else if ($this->rating >= 7) {
            $ratingClass = "Popular";
        } else {
            $ratingClass = "Regular";
        }
        return $ratingClass;
    }
}

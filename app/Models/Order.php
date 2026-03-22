<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $table = "orders";

    protected $primaryKey = "id";

    protected $fillable = [
        'user_id',
        'invoice_number',
        'total_price',
        'status'
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $casts = [
        'total_price' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // relasi ke user (buyer)
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}

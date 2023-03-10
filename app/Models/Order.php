<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'intrand_id',
        'product_id',
        'user_id',
        'order_date',
        'total_price',
        'status',
    ];

    public function intrand()
    {
        return $this->belongsTo(Intrand::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    
}

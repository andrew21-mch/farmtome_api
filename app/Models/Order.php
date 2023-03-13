<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'AgroInput_id',
        'product_id',
        'user_id',
        'order_date',
        'total_price',
        'status',
    ];

    public function AgroInput()
    {
        return $this->belongsTo(AgroInput::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class);
    }

    public function farm()
    {
        return $this->belongsTo(Farm::class);
    }

    public function supplierShop()
    {
        return $this->belongsTo(SupplierShop::class);
    }


}

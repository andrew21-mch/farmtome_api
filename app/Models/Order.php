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
        'customer_id',
        'supplier_shop_id',
        'farm_id',
        'status',
    ];

    public function AgroInput()
    {
        return $this->belongsTo(AgroInput::class, 'AgroInput_id');
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

    public function transaction()
    {
        return $this->hasOne(Transaction::class);
    }


}

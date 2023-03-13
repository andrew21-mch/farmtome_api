<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgroInput extends Model
{
    use HasFactory;

    // AgroInputs are farm tool or equipment
    // that are available for rent

    protected $fillable = [
        'name',
        'description',
        'price',
        'image',
        'supplier_id',
        'supplier_shop_id'
    ];

    public function user()
    {
        return $this->belongsTo(SupplierShop::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

}

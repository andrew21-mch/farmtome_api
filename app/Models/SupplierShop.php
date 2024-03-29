<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupplierShop extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'location',
        'supplier_id',
        'image',
    ];

    public function supplier()
    {
        return $this->belongsTo(User::class);
    }

    public function AgroInputs()
    {
        return $this->hasMany(AgroInput::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }


}

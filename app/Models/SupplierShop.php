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
    ];

    public function supplier()
    {
        return $this->belongsTo(User::class);
    }

    public function intrands()
    {
        return $this->hasMany(Intrand::class);
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded = ['id'];

    public function isAvailable($qty)
    {
        return $this->stock_available >= $qty;
    }

    public function sizes()
    {
        return $this->hasMany(ProductSize::class);
    }

    public function rentalItems()
    {
        return $this->hasMany(RentalItem::class);
    }
}

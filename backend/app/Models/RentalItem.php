<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RentalItem extends Model
{
    protected $guarded = ['id'];

    public $timestamps = false;

    public function rental()
    {
        return $this->belongsTo(Rental::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}

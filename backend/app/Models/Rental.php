<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    protected $guarded = ['id'];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'return_date' => 'datetime',
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }

    public function admin() {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function items() {
        return $this->hasMany(RentalItem::class);
    }
    
    public function payment() {
        return $this->hasOne(Payment::class);
    }
}

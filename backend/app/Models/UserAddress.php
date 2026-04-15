<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserAddress extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'recipient_name',
        'phone',
        'province',
        'city',
        'district',
        'postal_code',
        'full_address',
        'notes',
        'latitude',
        'longitude',
        'is_default',
    ];

    protected function casts(): array
    {
        return [
            'is_default' => 'boolean',
            'latitude'   => 'decimal:7',
            'longitude'  => 'decimal:7',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

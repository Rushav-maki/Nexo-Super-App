<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CarBooking extends Model
{
    protected $fillable = [
        'user_id',
        'car_id',
        'days',
        'price_per_day',
        'total_amount',
        'car_details',
    ];

    protected $casts = [
        'car_details' => 'array',
        'price_per_day' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function car()
    {
        return $this->belongsTo(Car::class);
    }
}

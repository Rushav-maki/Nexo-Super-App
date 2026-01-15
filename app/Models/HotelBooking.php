<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HotelBooking extends Model
{
    protected $fillable = [
        'user_id',
        'hotel_name',
        'location',
        'nights',
        'price_per_night',
        'total_amount',
        'rating',
        'amenities',
        'image_url',
        'hotel_details',
    ];

    protected $casts = [
        'amenities' => 'array',
        'hotel_details' => 'array',
        'price_per_night' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

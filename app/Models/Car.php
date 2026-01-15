<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    protected $fillable = [
        'name',
        'type',
        'price_per_day',
        'transmission',
        'seating_capacity',
        'fuel_type',
        'image_url',
        'features',
    ];
}

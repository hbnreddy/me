<?php

namespace App\Eloquent;

class CityWeather extends Model
{
    protected $fillable = [
        'city_id',
        'month',
        'high',
        'low',
        'average',
        'precipitation',
        'best_time',
    ];

    protected $casts = [
        'best_time' => 'boolean',
    ];
}

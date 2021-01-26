<?php

namespace App\Eloquent;

class CityPlace extends Model
{
    protected $fillable = [
        'city_id',
        'sygic_city_id',
        'sygic_place_id',
    ];
}

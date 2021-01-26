<?php

namespace App\Eloquent;

class PlaceTag extends Model
{
    protected $fillable = [
        'city_id',
        'place_id',
        'tag_key',
    ];
}

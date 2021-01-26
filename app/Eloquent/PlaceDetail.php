<?php

namespace App\Eloquent;

class PlaceDetail extends Model
{
    protected $fillable = [
        'place_id',
        'address',
        'email',
        'phone',
        'opening_hours',
        'is_deleted',
    ];

    protected $casts = [
        'is_deleted' => 'boolean',
    ];
}

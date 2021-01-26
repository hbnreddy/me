<?php

namespace App\Eloquent;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'identifier',
        'place_id',
        'activity_uuid',
        'city_id',
        'name',
        'type',
        'date',
        'start_time',
        'end_time',
        'total_price',
    ];
}

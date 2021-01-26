<?php

namespace App\Eloquent;

class EntityLocation extends Model
{
    protected $fillable = [
        'entity_id',
        'entity_type',
        'lat',
        'lng',
    ];
}

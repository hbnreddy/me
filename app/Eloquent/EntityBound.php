<?php

namespace App\Eloquent;

class EntityBound extends Model
{
    protected $fillable = [
        'entity_id',
        'entity_type',
        'south',
        'west',
        'north',
        'east',
    ];
}

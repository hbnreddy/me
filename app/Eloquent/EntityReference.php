<?php

namespace App\Eloquent;

class EntityReference extends Model
{
    protected $fillable = [
        'entity_id',
        'entity_type',
        'title',
        'type',
        'url',
    ];
}

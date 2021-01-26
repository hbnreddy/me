<?php

namespace App\Eloquent;

class EntityState extends Model
{
    protected $fillable = [
        'entity_id',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];
}

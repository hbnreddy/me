<?php

namespace App\Eloquent;

class MediaResource extends Model
{
    protected $fillable = [
        'entity_id',
        'entity_type',
        'url',
        'type',
    ];
}

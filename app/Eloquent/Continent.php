<?php

namespace App\Eloquent;

use App\EntityType;

class Continent extends Model
{
    protected $fillable = [
        'sygic_id',
        'name',
        'name_suffix',
        'description',
        'level',
        'rating',
        'rating_local',
        'marker',
        'thumbnail_url',
        'enabled',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function location()
    {
        return $this->hasOne(EntityLocation::class, 'entity_id')
            ->where('entity_type', EntityType::CONTINENT);
    }

    public function bounds()
    {
        return $this->hasOne(EntityBound::class, 'entity_id')
            ->where('entity_type', EntityType::CONTINENT);
    }
}

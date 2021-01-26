<?php

namespace App\Eloquent;

use App\EntityType;

class City extends Model
{
    protected $fillable = [
        'country_id',
        'sygic_id',
        'name',
        'name_suffix',
        'description',
        'level',
        'rating',
        'rating_local',
        'marker',
        'thumbnail_url',
        'image_url',
        'places_count',
        'unsecure_code',
        'enabled',
        'code',
    ];

    protected $appends = [
        'explored',
        'plane_ticket',
        'accommodation',
        'weathers',
    ];

    protected $casts = [
        'enabled' => 'boolean',
    ];

    public function places()
    {
        return $this->hasMany(Place::class, 'city_id');
    }

    public function location()
    {
        return $this->hasOne(EntityLocation::class, 'entity_id')
            ->where('entity_type', EntityType::CITY);
    }

    public function bounds()
    {
        return $this->hasOne(EntityBound::class, 'entity_id')
            ->where('entity_type', EntityType::CITY);
    }

    public function weather()
    {
        return $this->hasMany(CityWeather::class, 'city_id')
            ->where('best_time', true);
    }

    public function media_resources()
    {
        return $this->hasMany(MediaResource::class, 'entity_id');
    }

    public function getWeathersAttribute()
    {
        return $this->getRelationValue('weather')
            ->keyBy('month');
    }

    public function getExploredAttribute()
    {
        return false;
    }

    public function getPlaneTicketAttribute()
    {
        return 255.55;
    }

    public function getAccommodationAttribute()
    {
        return 125.5;
    }
}

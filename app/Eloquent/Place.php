<?php

namespace App\Eloquent;

use App\EntityType;

class Place extends Model
{
    protected $fillable = [
        'city_id',
        'marker_id',
        'venue_id',
        'sygic_id',
        'name',
        'name_suffix',
        'short_description',
        'description',
        'level',
        'rating',
        'rating_local',
        'marker',
        'thumbnail_url',
    ];

    protected $appends = [
        //
    ];

    public function details()
    {
        return $this->hasOne(PlaceDetail::class, 'place_id');
    }

    public function location()
    {
        return $this->hasOne(EntityLocation::class, 'entity_id')
            ->where('entity_type', EntityType::PLACE);
    }

    public function bounds()
    {
        return $this->hasOne(EntityBound::class, 'entity_id')
            ->where('entity_type', EntityType::PLACE);
    }

    public function references()
    {
        return $this->hasMany(EntityBound::class, 'entity_id')
            ->where('entity_type', EntityType::PLACE);
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function venue()
    {
        return $this->hasOne(Venue::class, 'place_id');
    }

    public function media_resources()
    {
        return $this->hasMany(MediaResource::class, 'entity_id')
            ->where('type', 'photo');
    }

    public function getThemeAttribute()
    {
        $marker = Marker::query()->find($this->marker_id);

        if (! $marker || ! $marker->theme_id) {
            return false;
        }

        return Theme::query()->find($marker->theme_id);
    }
}

<?php

namespace App\Eloquent;

use App\Http\Api\Musement\MusementApi;

class Venue extends Model
{
    protected $fillable = [
        'name',
        'place_id',
        'foreign_id',
        'country_id',
        'country_name',
        'city_id',
        'city_name',
    ];

    public function place()
    {
        return $this->belongsTo(Place::class, 'place_id');
    }

    public function getInformationAttribute()
    {
        $musementApi = resolve(MusementApi::class);

        return json_decode($musementApi->getVenue($this->foreign_id), true);
    }

    public function getActivitiesAttribute()
    {
        $musementApi = resolve(MusementApi::class);

        $activities = json_decode($musementApi->getVenueActivities($this->foreign_id, []), true);

        if (! $activities) {
            $activities = [];
        }

        foreach ($activities as &$activity) {
            $activityInfo =
                \GuzzleHttp\json_decode($musementApi->getActivity($activity['uuid']), true);

            $comments =
                \GuzzleHttp\json_decode($musementApi->getActivityComments($activity['uuid']), true);

            $media =
                \GuzzleHttp\json_decode($musementApi->getActivityMedia($activity['uuid']), true);

            if (! $activityInfo) {
                $activityInfo = [];
            }

            if (! $media) {
                $media = [];
            }

            if (! $comments) {
                $comments = [];
            }

            $activity = array_merge($activity, $activityInfo);
            $activity['comments'] = $comments;
            $activity['media'] = $media;
        }

        return $activities;
    }
}

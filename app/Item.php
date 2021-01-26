<?php

namespace App;

use App\Cart\TravellerType;
use App\Http\Api\Musement\MusementApi;
use Illuminate\Support\Str;

class Item
{
    public $id;
    public $name = '';
    public $type;
    public $date;
    public $timeslot;
    public $identifiers = [];
    public $travellers = [];
    public $musementItems = [];

    public function __construct()
    {
        $this->init();
    }

    public function init()
    {
        $this->id = Str::random();
    }

    public function getVenues()
    {
        $venues = [];
        if ($this->getType() === CartItemType::ACTIVITY) {
            $musementApi = resolve(MusementApi::class);

            $activity =
                \GuzzleHttp\json_decode($musementApi->getActivity($this->getAttribute('musement_id')), true);

            if ($activity) {
                foreach ($activity['venues'] as $venue) {
                    $venues[] = [
                        'id' => $venue['id'],
                        'name' => $venue['id'],
                        'meta_title' => $venue['meta_title'],
                        'short_description' => $venue['meta_description'],
                    ];
                }
            }
        }

        return $venues;
    }

    public function getPrice($type = TravellerType::ADULTS)
    {
        $key = 'price';
        if ($type !== TravellerType::ADULTS) {
            $key .= '_'.$type;
        }

        return isset($this->attributes[$key]) ? $this->attributes[$key] : 0;
    }

    public function placeId()
    {
        return intval($this->attributes['place_id']);
    }

    public function getAttribute($key)
    {
        return $this->attributes[$key];
    }

    public function getDate()
    {
        return $this->timeslot['date'];
    }

    public function getType()
    {
        return $this->attributes['type'];
    }

    public function setTravellers($travellers)
    {
        $this->travellers = $travellers;
    }

    public function setTimeslot($timeslot)
    {
        $this->timeslot = $timeslot;
    }

    public function toArray()
    {
        return array_merge($this->attributes,
            [
                'id' => $this->id,
                'timeslot' => $this->timeslot,
                'travellers' => $this->travellers,
            ]
        );
    }

    public function toDatabase()
    {
        //
    }
}

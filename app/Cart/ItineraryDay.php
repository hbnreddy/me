<?php

namespace App\Cart;

use App\Eloquent\City;

class ItineraryDay implements Representational
{
    public const BASE_DAY_HOURS = 18;

    protected $date = false;
    protected $remainingHours = 18;
    protected $cityId = false;

    /**
     * ItineraryDay constructor.
     */
    public function __construct($attributes)
    {
        $this->date = isset($attributes['date']) ? $attributes['date'] : false;
        $this->cityId = isset($attributes['city_id']) ? $attributes['city_id'] : false;
    }

    public function getRemainingHours()
    {
        return $this->remainingHours;
    }

    public function setRemainingHours($hours)
    {
        return $this->remainingHours = $hours;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setCity($cityId)
    {
        $this->cityId = $cityId;
    }

    public function getCity()
    {
        return $this->cityId;
    }

    public function toString()
    {
        return $this->getDate();
    }

    /**
     * @inheritDoc
     */
    public function toShort()
    {
        return [
            'city' => [
                'id' => $this->cityId,
                'name' => City::query()
                    ->where('id', $this->cityId)
                    ->pluck('name')
                    ->first(),
            ],
            'remaining_hours' => $this->remainingHours,
            'date' => $this->date,
        ];
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'city' => City::query()
                ->where('id', $this->cityId)
                ->pluck('name'),
            'remaining_hours' => $this->remainingHours,
            'date' => $this->date,
        ];
    }

    /**
     * @inheritDoc
     */
    public function toDatabase()
    {
        // TODO: Implement toDatabase() method.
    }
}

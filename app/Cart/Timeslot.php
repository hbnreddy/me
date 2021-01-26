<?php

namespace App\Cart;

class Timeslot implements Representational
{
    private $startDate;
    private $endDate;
    private $startTime;
    private $endTime;

    public function __construct($attributes)
    {
        $this->startDate = isset($attributes['start_date'])
            ? $attributes['start_date']
            : false;
        $this->endDate = isset($attributes['end_date'])
            ? $attributes['end_date']
            : false;
        $this->startTime = isset($attributes['start_time'])
            ? $attributes['start_time']
            : false;
        $this->endTime = isset($attributes['end_time'])
            ? $attributes['end_time']
            : false;
    }

    /**
     * @inheritDoc
     */
    public function toShort()
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ];
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ];
    }

    /**
     * @inheritDoc
     */
    public function toDatabase()
    {
        return [
            'start_date' => $this->startDate,
            'end_date' => $this->endDate,
            'start_time' => $this->startTime,
            'end_time' => $this->endTime,
        ];
    }
}

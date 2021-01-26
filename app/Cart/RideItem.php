<?php

namespace App\Cart;

use App\AppDateFormat;
use App\Eloquent\City;
use Carbon\Carbon;

class RideItem extends Item
{
    public const ONEWAY_TYPE = 'oneway';
    public const ROUND_TYPE = 'round';

    protected $vehicle_type;
    protected $duration;
    protected $from_city_id;
    protected $to_city_id;
    protected $from_city_code;
    protected $to_city_code;
    protected $booking_token;
    protected $flight_type;

    /**
     * ActivityItem constructor.
     */
    public function __construct()
    {
        parent::__construct(PlanItemType::RIDE_ITEM);
    }

    /**
     * @inheritDoc
     */
    public function setAttributes($attributes = [])
    {
        foreach ($attributes as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function getVehicleType()
    {
        return $this->vehicle_type;
    }

    public function getFromCityCode()
    {
        return isset($this->from_city_code)
            ? $this->from_city_code
            : City::query()->find($this->from_city_id)->code;
    }

    public function getToCityCode()
    {
        return isset($this->to_city_code)
            ? $this->to_city_code
            : City::query()->find($this->to_city_code)->code;
    }

    public function toShort()
    {
        $plan = $this->getPlan();
        $travellersIds = $this->getTravellersIds();

        $travellers = [];
        foreach ($plan->getTravellers() as $traveller) {
            if (in_array($traveller->getKey(), $travellersIds)) {
                $travellers[] = $traveller->toShort();
            }
        }

        $fromCityCode = $this->from_city_id
            ? City::query()->find($this->from_city_id)->code
            : $this->from_city_code;
        $toCityCode = $this->from_city_id
            ? City::query()->find($this->to_city_id)->code
            : $this->to_city_code;

        return [
            'id' => $this->getKey(),
            'type' => $this->type,
            'vehicle_type' => $this->vehicle_type,
            'from_city_code' => $fromCityCode,
            'to_city_code' => $toCityCode,
            'timeslot' => $this->getTimeslot()->toArray(),
            'travellers' => $travellers,
            'duration' => $this->duration,
        ];
    }

    public function getFromCityId()
    {
        return $this->from_city_id;
    }

    public function getToCityId()
    {
        return $this->to_city_id;
    }

    public function toUniqueId()
    {
        $data = $this->toArray();

        $rideTimeslot = $data['timeslot'];
        return $data['from_city_code']
            .'.'.$data['to_city_code']
            .'.'.$rideTimeslot['start_date']
            .'.'.$rideTimeslot['end_date'];
    }

    public function toApi()
    {
        $data = $this->toArray();

        $travellers = [
            'adults' => 0,
            'children' => 0,
            'infants' => 0,
        ];

        foreach ($data['travellers'] as $traveller) {
            $travellers[$traveller['type']]++;
        }

        return array_merge([
            'vehicle_type' => $this->vehicle_type,
            'fly_from' => $data['from_city_code'],
            'fly_to' => $data['to_city_code'],
            'date_from' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($data['timeslot']['start_date'])),
            'date_to' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($data['timeslot']['start_date'])),
            'return_from' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($data['timeslot']['start_date'])),
            'return_to' => date(AppDateFormat::RIDE_DATE_FORMAT, strtotime($data['timeslot']['start_date'])),
            'flight_type' => isset($data['flight_type']) ? $data['flight_type'] : self::ONEWAY_TYPE,
        ], $travellers);
    }

    public function toArray()
    {
        return $this->toShort();
    }

    public function toDatabase()
    {
        // TODO: Implement toArray() method.
    }
}

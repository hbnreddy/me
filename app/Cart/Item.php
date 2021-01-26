<?php

namespace App\Cart;

use App\Traits\HasExternalIds;
use App\Traits\HasKey;
use Illuminate\Support\Str;

abstract class Item implements Representational
{
    use HasKey, HasExternalIds;

    private $id;
    private $planId;
    protected $type;
    private $date;
    private $timeslot;
    private $externalIds = [];
    private $travellersIds = [];

    /**
     * Abstract Item constructor.
     */
    public function __construct($type)
    {
        $this->id = Str::random();
        $this->type = $type;
    }

    /**
     * Abstract method for setting attributes.
     */
    abstract public function setAttributes($attributes = []);

    /**
     * Setter for external ids.
     */
    public function setPlanId($id)
    {
        $this->planId = $id;
    }

    public function getTravellersCount()
    {
        $travellersCount = [
            'adults' => 0,
            'children' => 0,
            'infants' => 0,
        ];

        $plan = Cart::getPlan($this->getPlanId());
        $planTravellers = $plan->getTravellers();

        foreach ($planTravellers as $planTraveller) {
            if (in_array($planTraveller->getKey(), $this->getTravellersIds())) {
                $travellersCount[$planTraveller->getType()]++;
            }
        }

        return $travellersCount;
    }

    /**
     * Get type of the item.
     *
     * @return mixed
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Getter for external ids.
     */
    public function getPlanId()
    {
        return $this->planId;
    }

    /**
     * Getter for name if exists.
     */
    public function getName()
    {
        return isset($this->name) ? $this->name : '';
    }

    /**
     * Setter for travellers ids.
     */
    public function setTravellersIds($ids)
    {
        $this->travellersIds = $ids;
    }

    /**
     * Getter for travellers ids.
     */
    public function getTravellersIds()
    {
        return $this->travellersIds;
    }

    /**
     * Setter for item's date.
     */
    public function setDate($date)
    {
        $this->date = $date;
    }

    /**
     * Getter for item's date.
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Setter for item's date.
     */
    public function setTimeslot(Timeslot $timeslot)
    {
        $this->timeslot = $timeslot;
    }

    /**
     * Getter for item's date.
     */
    public function getTimeslot()
    {
        return $this->timeslot;
    }

    /**
     * Compute item's duration in minutes.
     */
    public function getDuration()
    {
        $timeslot = $this->timeslot->toArray();
        if (! $timeslot['start_time'] || ! $timeslot['end_time']) {
            return 0;
        }

        return (intval($timeslot['end_time']) - intval($timeslot['start_time'])) * 60;
    }

    public function travellers($shortDetails = false)
    {
        $plan = Cart::getPlan($this->planId);

        $travellers = [];
        foreach ($plan->getTravellers() as $traveller) {
            if (in_array($traveller->getKey(), $this->travellersIds)) {
                if ($shortDetails) {
                    $travellers[] = $traveller->toShort();
                } else {
                    $travellers[] = $traveller;
                }
            }
        }

        return $travellers;
    }

    public function getPlan()
    {
        return Cart::getPlan($this->planId);
    }
}

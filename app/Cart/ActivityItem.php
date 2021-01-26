<?php

namespace App\Cart;

class ActivityItem extends Item
{
    protected $name;
    protected $externalActivities = [];

    /**
     * ActivityItem constructor.
     */
    public function __construct()
    {
        parent::__construct(PlanItemType::ACTIVITY_ITEM);
    }

    /**
     * @inheritDoc
     */
    public function setAttributes($attributes = [])
    {
        $this->name = isset($attributes['name']) ? $attributes['name'] : '';
    }

    public function externalActivities()
    {
        return $this->externalActivities;
    }

    public function placesIds()
    {
        return $this->getExternalId('places_ids');
    }

    public function setExternalActivities($activities)
    {
        $this->externalActivities = $activities;
    }

    public function toShort()
    {
        /**
         * @var Plan $plan
         */
        $plan = $this->getPlan();
        $travellersIds = $this->getTravellersIds();

        $travellers = [];
        foreach ($plan->getTravellers() as $traveller) {
            if (in_array($traveller->getKey(), $travellersIds)) {
                $travellers[] = $traveller->toShort();
            }
        }

        return [
            'id' => $this->getKey(),
            'name' => $this->name,
            'type' => $this->type,
            'timeslot' => $this->getTimeslot()->toArray(),
            'travellers' => $travellers,
        ];
    }

    public function toArray()
    {
        // TODO: Implement toArray() method.
    }

    public function toDatabase()
    {
        // TODO: Implement toArray() method.
    }
}

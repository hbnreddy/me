<?php

namespace App\Cart;

use App\Cart\Representational;
use App\Traits\HasKey;
use Illuminate\Support\Str;

class Itinerary implements Representational
{
    use HasKey;

    protected $id;
    protected $roundTrip = true;
    protected $startNode = null;
    protected $lastNode = null;
    protected $cities = [];
    protected $dates = [];

    /**
     * Itinerary constructor.
     */
    public function __construct()
    {
        $this->id = Str::random();
    }

    public function isEmpty()
    {
        return $this->startNode === null;
    }

    public function &getStartNode()
    {
        return $this->startNode;
    }

    public function insert($data)
    {
        $node = new ItineraryNode();
        $node->setData($data);

        if (! $this->startNode) {
            $this->startNode = $node;
        } else {
            $currentNode = $this->startNode;
            while ($currentNode->hasNext()) {
                $currentNode = $currentNode->next();
            }

            $currentNode->setNext($node);
        }

        if (! in_array($data['from_city_id'], $this->cities)) {
            $this->insertCity($data['from_city_id']);
        }

        if (! $this->lastNode) {
            $this->lastNode = new ItineraryNode();
            $this->lastNode->setData([
                'from_city_id' => $data['to_city_id'],
                'to_city_id' => $data['from_city_id'],
                'timeslot' => null,
                'ride_id' => false,
            ]);
        } else {
            $lastNodeData = $this->lastNode->getData();
            $lastNodeData['from_city_id'] = $data['to_city_id'];
            $this->lastNode->setData($lastNodeData);
        }

        return true;
    }

    public function setDates($dates)
    {
        foreach ($dates as $date) {
            $this->dates[$date] = new ItineraryDay([
                'date' => $date,
                'city_id' => false,
            ]);
        }
    }

    public function lastDay()
    {
        return array_values($this->dates)[count($this->dates) - 1];
    }

    public function availableDates()
    {
        $dates = array_values($this->getDates());

        $index = count($dates) - 2;
        while (! $dates[$index]->getCity() && $dates[$index]->getRemainingHours() === ItineraryDay::BASE_DAY_HOURS && $index > 0) {
            $index--;
        }

        return array_splice($dates, $index, count($dates) - $index);
    }

    public function updateDates($dates)
    {
        $this->dates = $dates;
    }

    public function getDates()
    {
        return $this->dates;
    }

    public function cities()
    {
        return $this->cities;
    }

    public function insertCity($cityId)
    {
        if (! in_array($cityId, $this->cities)) {
            $this->cities[] = $cityId;
        }
    }

    public function getLastNode()
    {
        $node = $this->startNode;
        while ($node->hasNext()) {
            $node = $node->next();
        }

        return $node;
    }

    public function delete($id)
    {
        $previousNode = null;
        $currentNode = $this->startNode;

        while ($currentNode->getKey() !== $id) {
            $previousNode = $currentNode;
            $currentNode = $currentNode->next();
        }

        $nextNode = $currentNode->next();
        $previousNode->setNext($nextNode);

        unset($currentNode);

        return true;
    }

    public function isRoundTrip()
    {
        return $this->roundTrip;
    }

    /**
     * @inheritDoc
     */
    public function toShort()
    {
        $items = [];

        $node = $this->startNode;
        while ($node) {
            $nodeData = $node->toArray();
            $timeslot = $nodeData['timeslot']->toArray();

            $items[] = [
                'city_id' => $nodeData['to_city_id'],
                'date' => $timeslot['start_date'],
            ];

            $node = $node->next();
        }

        return $items;
    }

    /**
     * @inheritDoc
     */
    public function toArray()
    {
        $data = [
            'nodes' => [],
            'round_trip' => $this->roundTrip,
        ];

        $node = $this->startNode;
        while ($node) {
            $nodeData = $node->toArray();

            $data['nodes'][] = [
                'id' => $node->getKey(),
                'from_city_id' => $nodeData['from_city_id'],
                'to_city_id' => $nodeData['to_city_id'],
                'ride_id' => $nodeData['ride_id'],
                'travellers_ids' => $nodeData['travellers_ids'],
            ];
            $node = $node->next();
        }

        $lastNodeData = $this->lastNode->toArray();
        $data['nodes'][] = [
            'id' => $this->lastNode->getKey(),
            'from_city_id' => $lastNodeData['from_city_id'],
            'to_city_id' => $lastNodeData['to_city_id'],
            'ride_id' => $lastNodeData['ride_id'],
            'travellers_ids' => $nodeData['travellers_ids'],
        ];

        return $data;
    }

    /**
     * @inheritDoc
     */
    public function toDatabase()
    {
        // TODO: Implement toDatabase() method.
    }
}

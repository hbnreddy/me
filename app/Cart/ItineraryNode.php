<?php

namespace App\Cart;

use Illuminate\Support\Str;

class ItineraryNode
{
    public $id = 0;
    private $data = null;
    private $next = null;

    /**
     * Itinerary constructor.
     */
    public function __construct()
    {
        $this->id = Str::random();
    }

    public function getKey()
    {
        return $this->id;
    }

    public function setNext(ItineraryNode $node)
    {
        return $this->next = $node;
    }

    public function &next()
    {
        return $this->next;
    }

    public function hasNext()
    {
        return !!$this->next;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    public function getData()
    {
        return $this->data;
    }

    public function toArray()
    {
        return $this->data;
    }
}

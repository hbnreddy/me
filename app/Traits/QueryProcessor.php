<?php

namespace App\Traits;

use App\Cart\TravellerType;

trait QueryProcessor
{
    public function processTravellers($stringTravellers)
    {
        $exploded = explode('-', $stringTravellers);

        return [
            TravellerType::ADULTS => intval($exploded[0]),
            TravellerType::CHILDREN => intval($exploded[1]),
            TravellerType::INFANTS => intval($exploded[2]),
        ];
    }
}

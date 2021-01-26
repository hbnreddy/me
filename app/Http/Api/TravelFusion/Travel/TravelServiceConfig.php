<?php

namespace App\Http\Api\TravelFusion\Travel;

class TravelServiceConfig
{
    public const DATE_FORMAT = 'd/m/Y-H:i';

    public const ENTITY_TYPE = 'airportgroup';
    public const ENTITY_MODE = 'plane';

    public const TRAVEL_CLASSES = [
        'ewr' => 'Economy With Restrictions',
        'ewoutr' => 'Economy Without Restrictions',
        'ep' => 'Economy Premium',
        'b' => 'Business',
        'f' => 'First',
    ];

    public const AGE_MAPPING = [
        'adults' => 30,
        'children' => 12,
        'infants' => 12,
    ];

    public const GENDER_MAPPING = [
        'male' => 'Mr',
        'female' => 'Mrs',
    ];
}

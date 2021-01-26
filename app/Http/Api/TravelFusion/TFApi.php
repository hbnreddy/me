<?php

namespace App\Http\Api\TravelFusion;

use App\Http\Api\TravelFusion\Services\Flight\FlightService;
use App\Http\Api\TravelFusion\Services\Hotel\HotelService;
use GuzzleHttp\Client;

class TFApi
{
    private $config;

    private $flightService;

    private $hotelService;

    public function __construct($config, FlightService $flightService, HotelService $hotelService)
    {
        $this->config = $config;

        $this->flightService = $flightService;
        $this->hotelService = $hotelService;

        $client = new Client([
            'base_uri' => $config['api_host'],
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF-8',
                'Host' => $config['api_host'],
                'Accept-Encoding' => 'gzip',
            ],
            'cookies' => true,
        ]);

        $this->flightService->setClient($client);
        $this->hotelService->setClient($client);
    }

    public function flightService()
    {
        return $this->flightService;
    }

    public function hotelService()
    {
        return $this->hotelService;
    }
}

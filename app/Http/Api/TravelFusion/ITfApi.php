<?php

namespace App\Http\Api\TravelFusion;
use GuzzleHttp\Client;

interface ITfApi
{
    public function login();
    public function setClient(Client $client);
    public function credentials();
}

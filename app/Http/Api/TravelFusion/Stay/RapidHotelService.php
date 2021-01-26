<?php

namespace App\Http\Api\TravelFusion\Stay;

use App\Http\Api\TravelFusion\TfService;
use App\Normalizators\HotelNormalizator;
use App\Processors\RapidHotelProcessor;
use App\Traits\UseCacheKey;
use Illuminate\Support\Facades\Cache;

class RapidHotelService extends TfService
{
    use UseCacheKey;

    private $attempts = 3;

    protected $client;
    protected $credentials;

    protected $normalizator;
    protected $processor;

    public function __construct($client, $credentials)
    {
        $this->client = $client;
        $this->credentials = $credentials;
        $this->processor = new RapidHotelProcessor();
        $this->normalizator = new HotelNormalizator();
    }

    protected function credentials()
    {
        return $this->credentials;
    }

    private function login()
    {
        $xml = $this->processor->loginToXml($this->credentials());

        $response = $this->processor->xmlToArray($this->makeRequest($xml, HotelServiceConfig::RAPID_API_URI));

        return isset($response['@attributes'])
        && isset($response['@attributes']['token'])
            ? $response['@attributes']['token']
            : false;
    }

    public function token()
    {
        if (Cache::has(HotelServiceConfig::CACHE_TOKEN_KEY)) {
            return Cache::get(HotelServiceConfig::CACHE_TOKEN_KEY);
        }

        return $this->refreshToken();
    }

    private function refreshToken()
    {
        Cache::forget(HotelServiceConfig::CACHE_TOKEN_KEY);

        return Cache::remember(HotelServiceConfig::CACHE_TOKEN_KEY, 60 * 60 * 10, function () {
            return $this->login();
        });
    }

    private function sessionExpired($response)
    {
        return isset($response['@attributes']['errorCode']) && $response['@attributes']['errorCode'] === '1111';
    }

    public function fetchHotelsWithCache($parameters)
    {
        $searchKey = $this->cacheKey($parameters);
        $cachedSearch = Cache::get($searchKey);

        $filters = $parameters['extra']['filters'];

        $xml = $this->processor->hotelsWithFiltersToXml([
            '_attributes' => [
                'sid' => $cachedSearch['sid'],
                'token' => $this->token(),
            ],
            'filters' => $filters,
        ]);

        $requestParams = ['Cookie' => $cachedSearch['cookies']];

        $response = $this->makeRequest($xml, HotelServiceConfig::RAPID_API_URI, $requestParams, false);
        $response = $this->processor->xmlToArray($response);

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        if ($this->sessionExpired($response)) {
            Cache::forget($searchKey);
            $this->refreshToken();

            return [
                'message' => 'Session expired.',
                'polling' => true,
            ];
        }

        $completeConnections = intval($response['resultInfo']['@attributes']['completeConnections']);
        $supplierConnections = intval($response['resultInfo']['@attributes']['supplierConnections']);
        $polling = $completeConnections < $supplierConnections;

        if ($polling) {
            return [
                'message' => 'Data not loaded yet.',
                'polling' => true,
            ];
        }

        return [
            'entities' => $this->normalizator->normalizeHotels($response),
            'search_key' => $searchKey,
        ];
    }

    public function getHotel($parameters)
    {
        $xml = $this->processor->hotelQueryToXml(array_merge($parameters, [
            '_attributes' => [
                'token' => $this->token(),
            ],
        ]));

        $response = $this->makeRequest($xml, HotelServiceConfig::RAPID_API_URI, [
            'Cookie' => $parameters['cookies'],
        ]);

        $response = $this->processor->xmlToArray($response);

        return $this->normalizator->normalizeSingleHotel($response);
    }

    public function getBookingId($parameters)
    {
        $xml = $this->processor->getBookingIdToXml(array_merge($parameters, [
            '_attributes' => [
                'token' => $this->token(),
            ],
        ]));

        $response = $this->makeRequest($xml, HotelServiceConfig::RAPID_API_URI, [
            'Cookie' => $parameters['cookies'],
        ]);

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        $response = $this->processor->xmlToArray($response);

        return $this->normalizator->normalizeBookingIdResponse($response);
    }

    public function fetchHotels($parameters)
    {
        $searchKey = $this->cacheKey($parameters);
        $cachedSearch = Cache::get($searchKey);

        if (isset($cachedSearch)) {
            if (!$cachedSearch['cookies']) {
                Cache::forget($searchKey);

                if (!$this->attempts) {
                    return [
                        'message' => 'Cookies might be missing.',
                        'polling' => true,
                    ];
                }

                $this->attempts--;
                return $this->fetchHotels($parameters);
            }

            return $this->fetchHotelsWithCache($parameters);
        }

        $xml = $this->processor->hotelsQueryToXml(array_merge($parameters, [
            '_attributes' => [
                'token' => $this->token(),
            ],
        ]));

        $response = $this->makeRequest($xml, HotelServiceConfig::RAPID_API_URI, [], true);


        $headers = $response['headers'];
        if (isset($headers['set-cookie'])) {
            $content = $this->processor->xmlToArray($response['content']);

            $sid = $content['@attributes']['sid'];
            $cookies = $headers['set-cookie'][0];

            Cache::remember($searchKey, 60 * 60 * 2, function () use ($sid, $cookies) {
                return [
                    'sid' => $sid,
                    'cookies' => explode(';', $cookies)[0],
                ];
            });

            return $this->fetchHotels($parameters);
        }

        $this->attempts--;

        if ($this->attempts) {
            sleep(2);
            return $this->fetchHotels($parameters);
        }

        $this->attempts = 3;

        return [
            'message' => 'Setting up the session.',
            'polling' => true,
        ];
    }
}

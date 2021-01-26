<?php

namespace App\Http\Api\TravelFusion\Stay;

use App\Http\Api\TravelFusion\TfService;
use App\Normalizators\HotelNormalizator;
use App\Processors\HotelProcessor;
use App\Traits\UseCacheKey;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HotelService extends TfService implements IHotelService
{
    use UseCacheKey;

    protected $client;
    protected $config;

    protected $processor;
    protected $normalizator;
    protected $rapidService;

    /**
     * HotelService constructor.
     * @param $config
     */
    public function __construct($config)
    {
        $this->config = $config;
        $this->client = new Client([
            'base_uri' => $config['api_host'],
            'headers' => [
                'Content-Type' => 'text/xml; charset=UTF-8',
                'Host' => $config['api_host'],
                'Accept-Encoding' => 'gzip',
            ],
            'cookies' => true,
        ]);

        $this->processor = new HotelProcessor($this->credentials());
        $this->rapidService = new RapidHotelService($this->client, [
            'username' => $this->config['api_username'],
            'password' => $this->config['api_password'],
        ]);
        $this->normalizator = new HotelNormalizator();
    }

    /**
     * @return array|JsonResponse
     * @throws \Exception
     */
    protected function login()
    {
        return $this->makeRequest($this->processor->loginToXml($this->config));
    }

    protected function startRequest($parameters)
    {
        // TODO: Implement startRequest() method.
    }

    protected function checkRequest($routingId)
    {
        // TODO: Implement checkRequest() method.
    }

    /**
     * @param $parameters
     * @return mixed
     * @throws \Exception
     */
    public function all($parameters)
    {
        return $this->rapidService->fetchHotels($parameters);
    }

    /**
     * @param $parameters
     * @return mixed
     * @throws \Exception
     */
    public function getById($parameters)
    {
        $cachedData = Cache::get($parameters['search_key']);

        if (! isset($cachedData)) {
            throw new \Exception('Session has expired.');
        }

        return $this->rapidService->getHotel([
            'hotel_id' => $parameters['id'],
            'sid' => $cachedData['sid'],
            'cookies' => $cachedData['cookies'],
        ]);
    }

    /**
     * @param $parameters
     * @return array
     * @throws \Exception
     */
    public function getBookingId($parameters)
    {
        $cachedData = Cache::get($parameters['search_key']);

        if (! isset($cachedData)) {
            throw new \Exception('Session has expired.');
        }

        return $this->rapidService->getBookingId([
            'hotel_id' => $parameters['hotel_id'],
            'offer_id' => $parameters['offer_id'],
            'sid' => $cachedData['sid'],
            'cookies' => $cachedData['cookies'],
        ]);
    }

    /**
     * @param $parameters
     * @return array
     */
    public function cheapest($parameters)
    {
        $results = $this->getHotels(array_merge($parameters, [
            'extra' => [
                'ranking' => [
                    'sort_price' => '1.0',
                ],
                'filters' => [],
                'paging' => [],
            ],
        ]));

        $hotels = [];
        if (isset($results['entities'])) {
            $hotels = $results['entities'];
        }

        // Todo: Remove the check for Test Hotel.
        $cheapestHotelPrice = 99999999;
        foreach ($hotels as $hotel) {
            if ($hotel['name'] !== 'TEST HOTEL') {
                if ($hotel['price']['average'] < $cheapestHotelPrice) {
                    $cheapestHotelPrice = $hotel['price']['average'];
                } else {
                    break;
                }
            }
        }

        if ($cheapestHotelPrice === 99999999) {
            $cheapestHotelPrice = false;
        }

        return [
            'value' => $cheapestHotelPrice,
            'polling' => isset($results['polling']) ? $results['polling'] : false,
        ];
    }

    public function fetchHotels($parameters)
    {
        // TODO: Implement fetchHotels() method.
    }

    public function getHotel($parameters)
    {
        // TODO: Implement getHotel() method.
    }

    public function startDetails($parameters)
    {
        $xml = $this->processor->startDetailsToXml($parameters);

        $response = $this->processor->xmlToArray($this->makeRequest($xml));

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return $this->normalizator->normalizeStartDetails($response);
    }

    public function checkDetails($parameters)
    {
        $startDetails = $this->startDetails($parameters);

        $xml = $this->processor->checkDetailsToXml($parameters);

        $response = $this->makeRequest($xml);
        $response = $this->processor->xmlToArray($response);

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return $this->normalizator->normalizeCheckDetails($response);
    }

    public function startTerms($parameters)
    {
        $xml = $this->processor->startTermsToXml($parameters);

        $response = $this->processor->xmlToArray($this->makeRequest($xml));

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return $this->normalizator->normalizeStartTerms($response);
    }

    public function checkTerms($parameters)
    {
        $xml = $this->processor->checkTermsToXml($parameters);

        $response = $this->makeRequest($xml);
        $response = $this->processor->xmlToArray($response);

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return $this->normalizator->normalizeCheckTerms($response);
    }

    /**
     * Start the booking process.
     *
     * @param $parameters
     * @return array|JsonResponse
     * @throws \Exception
     */
    public function book($parameters)
    {
        $xml = $this->processor->bookRequestToXml($parameters);

        $response = $this->makeRequest($xml);

        $response = $this->processor->xmlToArray($response);

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return [
//            'booking_id' => $response['StartBooking']['TFBookingReference'],
        ];
    }

    /**
     * Get booking status.
     *
     * @param $parameters
     * @return array|JsonResponse
     * @throws \Exception
     */
    public function bookingStatus($parameters)
    {
        $xml = $this->processor->checkBookingToXml($parameters);

        $response = $this->processor->xmlToArray($this->makeRequest($xml));

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return $this->normalizator->normalizeCheckBooking($response);
    }

    /**
     * Start cancel process for a booking.
     *
     * @param $parameters
     * @return array|JsonResponse
     * @throws \Exception
     */
    public function cancelBooking($parameters)
    {
        $xml = $this->processor->cancelBookingToXml($parameters);
        $response = $this->processor->xmlToArray($this->makeRequest($xml));

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return [
            'booking_id' => $response['StartBookingCancelPlane']['TFBookingReference'],
        ];
    }

    /**
     * Start cancel process for a booking.
     *
     * @param $parameters
     * @return array|JsonResponse
     * @throws \Exception
     */
    public function cancelBookingStatus($parameters)
    {
        $xml = $this->processor->cancelBookingStatusToXml($parameters);
        $response = $this->processor->xmlToArray($this->makeRequest($xml));

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return [
            'booking_id' => $response['CheckBookingCancelPlane']['TFBookingReference'],
            'status' => $response['CheckBookingCancelPlane']['Status'],
        ];
    }
}

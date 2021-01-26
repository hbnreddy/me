<?php

namespace App\Http\Api\TravelFusion\Travel;

use App\Http\Api\TravelFusion\Stay\ITravelService;
use App\Http\Api\TravelFusion\TfService;
use App\Normalizators\TravelNormalizator;
use App\Processors\TravelProcessor;
use App\Traits\UseCacheKey;
use GuzzleHttp\Client;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class TravelService extends TfService implements ITravelService
{
    use UseCacheKey;

    /**
     * Service config
     */
    protected $config;

    protected $processor;
    protected $normalizator;

    /**
     * @var Client $client
     * Http Client
     */
    protected $client;

    public function __construct($config)
    {
        $this->config = $config;

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

        $this->processor = new TravelProcessor($this->credentials());
        $this->normalizator = new TravelNormalizator();
    }

    protected function login()
    {
        $xml = $this->processor->loginToXml([
            'username' => $this->config['api_flight_username'],
            'password' => $this->config['api_flight_password'],
        ]);

        $response = $this->makeRequest($xml);

        return $this->processor->xmlToArray($response);
    }

    protected function startRequest($parameters)
    {
        $xml = $this->processor->startRoutingToXml($parameters);

        $response = $this->processor->xmlToArray($this->makeRequest($xml));

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return $response['StartRouting'];
    }

    protected function checkRequest($routingId)
    {
        $xml = $this->processor->checkRoutingToXml([
            'CheckRouting' => array_merge($this->credentials(), [
                'RoutingId' => $routingId,
            ]),
        ]);

        $response = $this->processor->xmlToArray($this->makeRequest($xml));

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return $response['CheckRouting'];
    }

    /**
     * Fetch all routes depending on the given parameters.
     *
     * @param array $parameters
     * @return array
     * @throws \Exception
     */
    public function all($parameters)
    {
        $startRouting = $this->startRequest($parameters);
        $routingId = $startRouting['RoutingId'];

        $checkRouting = $this->checkRequest($routingId);

        $routers = $this->normalizator->assocToArray($checkRouting['RouterList']['Router']);

        $travelData = $this->normalizator->normalizeTravels($routers);

        $outwards = array_map(function ($outward) use ($routingId) {
            return array_merge($outward, [
                'routing_id' => $routingId,
            ]);
        }, $travelData['outwards']);

        $returns = array_map(function ($outward) use ($routingId) {
            return array_merge($outward, [
                'routing_id' => $routingId,
            ]);
        }, $travelData['returns']);

        return [
            'outwards' => $outwards,
            'returns' => $returns
        ];
    }

    /**
     * Process groups of flights.
     *
     * @param array $parameters
     * @param array $filters
     * @return array|mixed
     */
    public function groups($parameters = [], $filters = [])
    {
        $routes = $parameters['routes'];
        $limit = $parameters['query']['limit'];
        $offset = $parameters['query']['offset'];

        if (!count($routes)) {
            return [];
        }

        $cacheKey = $this->cacheKey($parameters, 'travel');

        Cache::forget($cacheKey);
        return Cache::remember($cacheKey, 60, function () use ($routes, $filters, $limit, $offset) {
            $rides = [];
            foreach ($routes as $route) {
                $startRouting = $this->startRequest($route);

                $routingId = $startRouting['RoutingId'];

                $checkRouting = $this->checkRequest($routingId);

                $routers = $this->normalizator->assocToArray($checkRouting['RouterList']['Router']);

                $rides[] = [
                    'suppliers_groups' => $this->normalizator->normalizeRouters($routers, $routingId),
                    'departure_city_code' => $route['departure_city_code'],
                    'arrival_city_code' => $route['arrival_city_code'],
                ];
            }

            $groups = $this->normalizator->normalizeTravelGroups($rides);

            foreach ($groups as &$group) {
                $groupKey = implode('.', array_map(function ($el) {
                    return $el['id'];
                }, $group));

                foreach ($group as &$item) {
                    $item['group_id'] = $groupKey;
                }
            }

            /**
             * Sorting & filtering by price.
             */
            $prices = array_map(function ($flights) {
                return array_sum(array_map(function ($flight) {
                    return floatval($flight['price']['amount']);
                }, $flights));
            }, $groups);

            if (isset($filters['max_price'])) {
                $maxPrice = $filters['max_price'];

                $groups = array_filter($groups, function ($el, $index) use ($prices, $maxPrice) {
                    return $prices[$index] <= $maxPrice;
                }, ARRAY_FILTER_USE_BOTH);
            }

            uksort($groups, function ($i, $j) use ($prices) {
                return $prices[$i] >= $prices[$j] ? 1 : -1;
            });

            return array_slice(array_values($groups), $offset, $limit);
        });
    }

    /**
     * Process cheapest ride price.
     *
     * TODO: Check return route.
     *
     * @param array $parameters
     * @return array|bool|float|JsonResponse|int|mixed
     * @throws \Exception
     */
    public function cheapest($parameters)
    {
        $entities = $this->all($parameters);

        $cheapestOutward = [];
        $cheapestReturn = [];

        foreach ($entities['outwards'] as $entity) {
            if (!$cheapestOutward || $entity['price']['amount'] < $cheapestOutward['price']['amount']) {
                $cheapestOutward = $entity;
            }
        }

        foreach ($entities['returns'] as $entity) {
            if (!$cheapestReturn || $entity['price']['amount'] < $cheapestReturn['price']['amount']) {
                $cheapestReturn = $entity;
            }
        }

        if ($cheapestOutward && ($parameters['price_only'] ?? null)) {
            $amount = $cheapestOutward['price']['amount'] + ($cheapestReturn ? $cheapestReturn['price']['amount'] : 0);

            $currency = $cheapestOutward['price']['currency'];
            if ($cheapestReturn && $cheapestOutward['price']['currency'] != $cheapestReturn['price']['currency']) {
                $currency = $cheapestOutward['price']['currency'] . '/' . $cheapestReturn['price']['currency'];
            }

            return [
                'amount' => $amount,
                'currency' => $currency
            ];
        }

        return [
            'outward' => $cheapestOutward,
            'return' => $cheapestReturn
        ];
    }

    /**
     * Process terms of a an Outward.
     *
     * @param $parameters
     * @return array|JsonResponse
     * @throws \Exception
     */
    public function terms($parameters)
    {
        $xml = $this->processor->processDetailsToXml($parameters);

        $response = $this->processor->xmlToArray($this->makeRequest($xml));

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return $this->normalizator->normalizeTravelTerms($response);
    }

    /**
     * Submit traveller details for a flight router.
     *
     * @param $parameters
     * @return array|JsonResponse|string
     * @throws \Exception
     */
    public function process($parameters)
    {
        $xml = $this->processor->processRequestToXml($parameters);

        $response = $this->makeRequest($xml);

        $response = $this->processor->xmlToArray($response);

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return $this->normalizator->normalizeProcessTerms($response);
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
            'booking_id' => $response['StartBooking']['TFBookingReference'],
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
        $xml = $this->processor->bookingStatusToXml($parameters);

        $response = $this->processor->xmlToArray($this->makeRequest($xml));

        $errors = $this->normalizator->normalizeErrors($response);

        return $errors;
        if ($errors) {
            throw new \Exception($errors);
        }

        return $this->normalizator->normalizeBookingStatus($response);
    }

    /**
     * Get booking details.
     *
     * @param $parameters
     * @return array|JsonResponse
     * @throws \Exception
     */
    public function bookingDetails($parameters)
    {
        $xml = $this->processor->bookingDetailsToXml($parameters);
        $response = $this->processor->xmlToArray($this->makeRequest($xml));

        $errors = $this->normalizator->normalizeErrors($response);
        if ($errors) {
            throw new \Exception($errors);
        }

        return $this->normalizator->normalizeBookingDetails($response);
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

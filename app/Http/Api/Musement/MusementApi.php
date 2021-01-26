<?php

namespace App\Http\Api\Musement;

use App\AppCache;
use App\AppConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class MusementApi
{
    private $client = null;

    public function __construct($config = null)
    {
        $this->client = new Client([
            'base_uri' => $config['api_url'],
            'headers' => [
                'accept' => 'application/json',
                'accept-language' => 'en-US',
                'client_id' => $config['api_key'],
                'client_secret' => $config['api_secret'],
            ],
        ]);
    }

    public function getVenues($parameters = [])
    {
        if (!isset($parameters['limit'])) {
            $parameters['limit'] = MusementConfig::DEFAULT_LIMIT;
        }

        $query = http_build_query($parameters);

        try {
            $request = new Request('GET',
                MusementRoute::VENUES . '?' . $query,
                [
                    'X-Musement-Currency' => AppConfig::getCurrency(),
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getVenue($venueId)
    {
        try {
            $request = new Request('GET', MusementRoute::VENUES . '/' . $venueId, [
                'accept' => 'application/json',
                'X-Musement-Currency' => AppConfig::getCurrency(),
                'Accept-Language' => strtoupper(AppConfig::getLocale()),
            ]);

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getVenueActivities($venueId, $filters = [])
    {
        $query = '?';

        $limit = MusementConfig::DEFAULT_LIMIT;

        if (isset($filters['limit'])) {
            $limit = $filters['limit'];
        }

        $query .= "limit=${limit}";

        if (isset($filters['start_date'])) {
            $query .= "&date_from=${filters['start_date']}";
        }

        if (isset($filters['end_date'])) {
            $query .= "&date_to=${filters['end_date']}";
        }

        if (isset($filters['offset'])) {
            $query .= "&offset=${filters['offset']}";
        }

        $route = MusementRoute::VENUES . '/' . $venueId . '/' . MusementRoute::ACTIVITIES . $query . '&venue_in=' . $venueId;

        try {
            $request = new Request('GET',
                $route,
                [
                    'X-Musement-Currency' => AppConfig::getCurrency(),
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getVenueComments($venueId)
    {
        try {
            $request = new Request('GET',
                MusementRoute::VENUES . '/' . $venueId . '/' . MusementRoute::VENUE_COMMENTS,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getActivityDates($activityId, $params)
    {
        $query = '?';
        $hasQuery = false;
        if (isset($params['start_date'])) {
            $query = $query . 'date_from=' . $params['start_date'];
            $hasQuery = true;
        }

        if (isset($params['end_date'])) {
            if ($hasQuery) {
                $query .= '&';
            }

            $query = $query . 'date_to=' . $params['end_date'];
            $hasQuery = true;
        }

        try {
            $request = new Request('GET',
                MusementRoute::ACTIVITIES . '/' . $activityId . '/dates' . $query,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getActivityDateInfo($activityId, $date)
    {
//        $query = '?tickets_number=1'; // for real time activities
//        $query .= '&pickup=rome'; // for pickup activities

        try {
            $request = new Request('GET',
                MusementRoute::ACTIVITIES . '/' . $activityId . '/dates/' . $date,
                [
                    'X-Musement-Currency' => AppConfig::getCurrency(),
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getActivity($activityId)
    {
        try {
            $request = new Request('GET',
                MusementRoute::ACTIVITIES . '/' . $activityId,
                [
                    'X-Musement-Currency' => AppConfig::getCurrency(),
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getActivityTaxonomies($activityId)
    {
        try {
            $request = new Request('GET',
                MusementRoute::ACTIVITIES . '/' . $activityId . '/taxonomies',
                [
                    'X-Musement-Currency' => AppConfig::getCurrency(),
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getActivityRefundPolicies($activityId)
    {
        try {
            $request = new Request('GET',
                MusementRoute::ACTIVITIES . '/' . $activityId . '/refund-policies',
                [
                    'X-Musement-Currency' => AppConfig::getCurrency(),
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getActivityComments($activityId)
    {
        try {
            $request = new Request('GET',
                MusementRoute::ACTIVITIES . '/' . $activityId . '/' . MusementRoute::ACTIVITY_COMMENTS,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getActivityMedia($activityId)
    {
        try {
            $request = new Request('GET',
                MusementRoute::ACTIVITIES . '/' . $activityId . '/' . MusementRoute::ACTIVITY_MEDIA,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getCurrencies()
    {
        try {
            $request = new Request('GET',
                MusementRoute::CURRENCIES
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getLanguages()
    {
        try {
            $request = new Request('GET',
                MusementRoute::LANGUAGES
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function login()
    {
        $config = [
            'client_id' => $this->client->getConfig('headers')['client_id'],
            'client_secret' => $this->client->getConfig('headers')['client_secret'],
        ];

        $query = "?client_id=${config['client_id']}&client_secret=${config['client_secret']}&grant_type=client_credentials";

        try {
            $request = new Request('POST',
                MusementRoute::LOGIN.$query,
                []
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getCart($id)
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        try {
            $request = new Request('GET',
                MusementRoute::CART."/${id}".$query,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                    'X-Musement-Currency' => AppConfig::getCurrency(),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function createCart()
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        try {
            $request = new Request('POST',
                MusementRoute::CART.$query,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                    'Content-Type' => 'application/json',
                ],
                '{}'
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function addItemToCart($cartId, $item)
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        try {
            $request = new Request('POST',
                MusementRoute::CART."/${cartId}/items".$query,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                    'X-Musement-Currency' => AppConfig::getCurrency(),
                    'Content-Type' => 'application/json',
                ],
                json_encode($item)
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function removeItemFromCart($cartId, $itemUuid)
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        try {
            $request = new Request('DELETE',
                MusementRoute::CART."/${cartId}/items/${itemUuid}".$query
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return json_encode(false);
        }

        return $response->getStatusCode() === 204;
    }

    public function putTravellerInCart($cartId, $itemUuid, $items)
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        $body = json_encode($items);

        try {
            $request = new Request('PUT',
                MusementRoute::CART."/${cartId}/items/${itemUuid}/participants".$query,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                    'Content-Type' => 'application/json',
                ],
                $body
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return $response->getBody()->getContents();
    }

    public function getOrder($orderId)
    {
        /**
         * "identifier" => "MUS1266871"
         *  "uuid" => "8c66fd24-4942-43e4-ba0e-7879eb17f870"
         */

        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        try {
            $request = new Request('GET',
                MusementRoute::ORDER."/${orderId}".$query,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                    'X-Musement-Currency' => AppConfig::getCurrency(),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return $response->getBody()->getContents();
    }

    public function getOrders()
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        try {
            $request = new Request('GET',
                MusementRoute::ALL_ORDERS.$query,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                    'X-Musement-Currency' => AppConfig::getCurrency(),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return $response->getBody()->getContents();
    }

    public function setCustomer($cartId, $customer)
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        $attributes = $customer->attributes;
        $customerSchema = json_decode($this->customerSchema($cartId), true);

        $requiredAppends = [
            'city' => 'Iasi',
            'address' => 'My address.',
            'tax_id' => '55512',
        ];

        $appends = [
            'musement_newsletter' => 'NO',
            'allow_profiling' => 'NO',
            'thirdparty_newsletter' => 'NO',
            'events_related_newsletter' => 'NO',
        ];

        $keyMap = [
            'firstname' => 'first_name',
            'lastname' => 'last_name',
            'email' => 'email',
            'zipcode' => 'pincode',
            'city' => 'city',
        ];

        $requiredFields = $customerSchema['required'];

        $customerInfo = [];
        foreach ($requiredFields as $field) {
            if (isset($keyMap[$field]) && isset($attributes[$keyMap[$field]])) {
                $customerInfo[$field] = $attributes[$keyMap[$field]];
            }
        }

        $extraDataKeyMap = [
            'please_insert_your_mobile_number' => 'mobile_number',
        ];

        $extraCustomerData = [];
        if (in_array('extra_customer_data', $requiredFields)) {
            $data = $customerSchema['properties']['extra_customer_data'];

            foreach ($data['required'] as $itemUuid) {
                $extraCustomerData[$itemUuid] = [];

                foreach ($data['properties'][$itemUuid]['required'] as $field) {
                    $extraCustomerData[$itemUuid][$field] = $attributes[$extraDataKeyMap[$field]];
                }
            }

            $customerInfo['extra_customer_data'] = $extraCustomerData;
        }

        $customerInfo = array_merge($customerInfo, $appends);
        $body = json_encode($customerInfo);

        try {
            $request = new Request('PUT',
                MusementRoute::CART."/${cartId}/customer".$query,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                    'Content-Type' => 'application/json',
                ],
                $body
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return $response->getBody()->getContents();
    }

    public function participantsSchema($cartId, $itemUuid)
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        try {
            $request = new Request('GET',
                MusementRoute::CART."/${cartId}/items/${itemUuid}/participants/schema".$query
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return $response->getBody()->getContents();
    }

    public function itemParticipants($cartId, $itemUuid)
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        try {
            $request = new Request('GET',
                MusementRoute::CART."/${cartId}/items/${itemUuid}/participants".$query,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                ]
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return $response->getBody()->getContents();
    }

    public function cartForm($cartId)
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        try {
            $request = new Request('GET',
                MusementRoute::CART."/${cartId}/form"
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return $response->getBody()->getContents();
    }

    public function customerSchema($cartId)
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        try {
            $request = new Request('GET',
                MusementRoute::CART."/${cartId}/customer/schema"
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return $response->getBody()->getContents();
    }

    public function createOrder($cartId)
    {
        $credentials = AppCache::musementCredentials();
        $query = "?access_token=${credentials['access_token']}";

        try {
            $request = new Request('POST',
                MusementRoute::ORDER,
                [
                    'Accept-Language' => strtoupper(AppConfig::getLocale()),
                    'X-Musement-Currency' => AppConfig::getCurrency(),
                    'Content-Type' => 'application/json',
                ],
                json_encode([
                    'cart_uuid' => $cartId,
                ])
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return $response->getBody()->getContents();
    }
}

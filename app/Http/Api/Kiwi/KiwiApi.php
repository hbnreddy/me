<?php

namespace App\Http\Api\Kiwi;

use App\AppConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class KiwiApi
{
    private $client = null;

    public function __construct($config = null)
    {
        $this->client = new Client([
            'base_uri' => $config['api_url'],
            'headers' => [
                'api_key' => $config['api_key'],
                'api_multi_key' => $config['api_multi_key'],
//                'accept-language' => 'en-US',
//                'client_id' => $config['api_key'],
//                'client_secret' => $config['api_secret'],
            ],
        ]);
    }

    public function getById($id, $helpers = [])
    {
        $results = \GuzzleHttp\json_decode($this->search($helpers), true);
        if (! $results || ! count($results['data'])) {
            return \GuzzleHttp\json_encode(['success' => false]);
        }

        foreach ($results['data'] as $result) {
            if ($result['id'] === $id) {
                return \GuzzleHttp\json_encode([
                    'success' => true,
                    'entity' => $result,
                ]);
            }
        }

        return \GuzzleHttp\json_encode(['success' => false]);
    }

    public function search($parameters = [])
    {
        if (! isset($parameters['vehicle_type'])) {
            $parameters['vehicle_type'] = 'aircraft,train';
        }

        $parameters = array_merge($parameters, [
            'apikey' => $this->client->getConfig('headers')['api_key'],
            'sort' => isset($parameters['sort']) ? $parameters['sort'] : 'quality',
            'asc' => 1,
            'curr' => strtoupper(AppConfig::getCurrency()),
//            'limit' => 1,
        ]);

        $query = '?';
        foreach ($parameters as $key => $value) {
            if (isset($value)) {
                $query = $query.$key.'='.$value.'&';
            }
        }

        $query = substr($query, 0, strlen($query) - 1);

        try {
            $request = new Request('GET',
                KiwiRoute::SEARCH.$query
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function multiSearch($parameters = [])
    {
        $jsonData = [
            'requests' => $parameters['body'],
        ];

        $sort = isset($parameters['sort']) ? $parameters['sort'] : 'price';
        $query = '?apikey='.$this->client->getConfig('headers')['api_multi_key']
            .'&curr='.strtoupper(AppConfig::getCurrency())
            .'&sort='.$sort;

        try {
            $request = new Request('POST',
                KiwiRoute::SEARCH_MULTI.$query,
                    [],
                    \GuzzleHttp\json_encode($jsonData)
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function airports()
    {
        $query = '?apikey='.$this->client->getConfig('headers')['api_key'];
        $query .= '&term=airport';
        $query .= '&sort=rank';
        $query .= '&limit=12';

        try {
            $request = new Request('GET',
                KiwiRoute::LOCATIONS.'/top-destinations'.$query
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function locations($parameters = [])
    {
        if (! isset($parameters['term'])) {
            return \GuzzleHttp\json_encode(false);
        }

        if (! isset($parameters['location_types'])) {
            $parameters['location_types'] = 'city';
        }

        $query = '?apikey='.$this->client->getConfig('headers')['api_key'];
        $query .= '&location_types='.$parameters['location_types'];
        $query .= '&term='.$parameters['term'];

        try {
            $request = new Request('GET',
                KiwiRoute::LOCATIONS.'/query'.$query
            );

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            dd($exception->getMessage());

            return json_encode(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }
}

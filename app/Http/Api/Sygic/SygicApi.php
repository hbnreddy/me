<?php

namespace App\Http\Api\Sygic;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

class SygicApi
{
    private $client = null;

    public function __construct ($config)
    {
        $this->client = new Client([
            'base_uri' => $config['api_url'],
            'headers' => [
                'x-api-key' => $config['api_key'],
            ],
        ]);
    }

    public function getContinents($filters = [])
    {
        if (! isset($filters['limit'])) {
            $filters['limit'] = SygicConfig::DEFAULT_LIMIT;
        }

        if (! isset($filters['levels'])) {
            $filters['levels'] = implode('|', [SygicLevel::CONTINENT]);
        }

        try {
            $request = new Request('GET', SygicRoute::PLACES_LIST.'?'.http_build_query($filters));

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return response()->json(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getCountries($filters = [])
    {
        if (! isset($filters['limit'])) {
            $filters['limit'] = SygicConfig::DEFAULT_LIMIT;
        }

        $filters['levels'] = implode('|', [SygicLevel::COUNTRY]);

        if (isset($filters['parents']) && is_array($filters['parents'])) {
            $filters['parents'] = implode('|', $filters['parents']);
        }

        try {
            $request = new Request('GET', SygicRoute::PLACES_LIST.'?'.http_build_query($filters));

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return response()->json(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getCities($filters = [])
    {
        if (! isset($filters['limit'])) {
            $filters['limit'] = SygicConfig::DEFAULT_LIMIT;
        }

        $filters['levels'] = implode('|', [SygicLevel::CITY]);

        if (isset($filters['parents']) && is_array($filters['parents'])) {
            $filters['parents'] = implode('|', $filters['parents']);
        }

        try {
            $request = new Request('GET', SygicRoute::PLACES_LIST.'?'.http_build_query($filters));

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return response()->json(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getPlacesOfInterest($filters = [])
    {
        if (! isset($filters['limit'])) {
            $filters['limit'] = SygicConfig::DEFAULT_LIMIT;
        }

        $filters['levels'] = implode('|', [SygicLevel::POI]);

        if (isset($filters['parents']) && is_array($filters['parents'])) {
            $filters['parents'] = implode('|', $filters['parents']);
        }

        if (isset($filters['tags']) && is_array($filters['tags'])) {
            $filters['tags'] = implode('|', $filters['tags']);
        }

        try {
            $request = new Request('GET', SygicRoute::PLACES_LIST.'?'.http_build_query($filters));

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return response()->json(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getPlacesByIds($placeIds)
    {
        $query = 'ids='.implode('|', $placeIds);

        try {
            $request = new Request('GET', SygicRoute::PLACE_DETAILS.'?'.$query);

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return response()->json(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getPlace($id)
    {
        $level = explode(':', $id)[0];

        try {
            $request = new Request('GET', SygicRoute::PLACE_DETAILS."/{$id}?levels={$level}");

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return response()->json(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function searchPlace($query, $level)
    {
        try {
            $request = new Request('GET', SygicRoute::PLACES_LIST."?levels={$level}&query={$query}");

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return response()->json(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }

    public function getCollections($filters)
    {
        if (! isset($filters['limit'])) {
            $filters['limit'] = SygicConfig::DEFAULT_COLLECTIONS_LIMIT;
        }

        try {
            $request = new Request('GET', SygicRoute::COLLECTIONS.'?'.http_build_query($filters));

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            return response()->json(false);
        }

        return response($response->getBody()->getContents())->getContent();
    }
}

<?php

namespace App\Http\Api\TravelFusion;

use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Psr7\Request;

abstract class TfService
{
    protected $client;
    protected $config;

    protected function credentials()
    {
        return [
            'LoginId' => $this->config['api_login_id'],
            'XmlLoginId' => $this->config['api_xml_login_id'],
        ];
    }

    protected function makeRequest($xml, $uri = '/Xml', $setHeaders = [], $returnHeaders = false)
    {
        try {
            $request = new Request('POST', $uri, $setHeaders, $xml);

            $response = $this->client->send($request);
        } catch (RequestException $exception) {
            throw new \Exception('Invalid request: ' . $exception->getMessage());
        }

        if ($returnHeaders) {
            return [
                'headers' => $response->getHeaders(),
                'content' => $response->getBody()->getContents(),
            ];
        }

        return $response->getBody()->getContents();
    }
}

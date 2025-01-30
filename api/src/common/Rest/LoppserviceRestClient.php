<?php

namespace App\common\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Promise\PromiseInterface;
use Psr\Http\Message\ResponseInterface;

class LoppserviceRestClient
{
    private Client $client;
    private array $headers;

    public function __construct()
    {
       $this->headers =  ['apikey' => 'testkey', 'accept' => 'application/json'];
        $this->client = new Client([
            'base_uri' => 'http://ebrevet.org/loppservice/public/api/integration',
            'timeout'  => 10.0,
            'headers'  => ['apikey' => 'apikey', 'accept' => 'application/json'],
        ]);
    }

    private function handleResponse(PromiseInterface $promise): PromiseInterface
    {
        return $promise->then(
            function (ResponseInterface $response) {
                return ['success' => true, 'data' => json_decode($response->getBody(), true)];
            },
            function (GuzzleException $e) {
                return ['success' => false, 'error' => $e->getMessage()];
            }
        );
    }

    public function getAsync(string $endpoint, array $queryParams = []): PromiseInterface
    {
        return $this->handleResponse($this->client->getAsync($endpoint, [
            'query' => $queryParams,
            'headers' => $this->headers,
        ]));
    }

    public function postAsync(string $endpoint, array $body = []): PromiseInterface
    {
        return $this->handleResponse($this->client->postAsync($endpoint, [
            'json' => $body,
            'headers' => $this->headers,
        ]));
    }

    public function putAsync(string $endpoint, array $body = []): PromiseInterface
    {
        return $this->handleResponse($this->client->putAsync($endpoint, [
            'json' => $body,
            'headers' => $this->headers,
        ]));
    }
}

//// Example Usage
// usage http://ebrevet.org/loppservice/public/api/integration/event/all
// http://localhost:8080/loppservice/api/integration/event/all
// inside container http://app:80/loppservice
//$client = new LoppserviceRestClient();
//
// Example Usage
//$client = new EbrevetClient(['Authorization' => 'Bearer YOUR_TOKEN']);
//
//// Performing a GET request
//$client->getAsync('/some-endpoint')->then(
//    function ($result) {
//        if ($result['success']) {
//            echo json_encode($result['data']);
//        } else {
//            echo 'Error: ' . $result['error'];
//        }
//    }
//)->wait();
//
//// Performing a POST request
//$client->postAsync('/some-endpoint', ['key' => 'value'])->then(
//    function ($result) {
//        if ($result['success']) {
//            echo json_encode($result['data']);
//        } else {
//            echo 'Error: ' . $result['error'];
//        }
//    }
//)->wait();


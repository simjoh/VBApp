<?php

namespace App\common\Rest;


use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Promise\PromiseInterface;

class AcpReportRestClient
{
    private Client $client;

    public function __construct()
    {
        $this->client = new Client([
            'base_uri' => 'https://acp-dev.kcorp.be/api/brm/',
            'timeout' => 10.0,
        ]);
    }

    public function sendAthletesDataAsync(int $brmId, array $athletes): PromiseInterface
    {
        return $this->client->postAsync("{$brmId}/homologations", [
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json',
            ],
            'json' => $athletes,
        ])->then(
            function ($response) {
                return [
                    'status_code' => $response->getStatusCode(),
                    'body' => json_decode($response->getBody(), true),
                ];
            },
            function (RequestException $e) {
                return [
                    'error' => $e->getMessage(),
                    'response' => $e->hasResponse() ? $e->getResponse()->getBody()->getContents() : null,
                ];
            }
        );
    }
}

// usage

//$promise = $client->sendAthletesDataAsync(48479, $athletes);
//
//// Wait for the response and output it
//$response = $promise->wait();
//echo json_encode($response, JSON_PRETTY_PRINT);
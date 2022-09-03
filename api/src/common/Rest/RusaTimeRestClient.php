<?php

namespace App\common\Rest;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

class RusaTimeRestClient
{

    public function __construct(string $rusaurl = '')
    {
        $this->rusaurl = $rusaurl;
        $this->client = new Client();
    }


    public function post(string $payload): ResponseInterface
    {
        return $this->client->request('POST', $this->rusaurl . $payload);
    }


    public function postAsync(string $payload)
    {
        $promise = $this->client->requestAsync('POST', $this->rusaurl . $payload);
        return $promise->then(
            function (ResponseInterface $res) {
                return $res->getBody()->getContents();
            },
            function (RequestException $e) {
                echo $e->getMessage() . "\n";
                echo $e->getRequest()->getMethod();
            }
        )->wait();
    }

    public function get(string $payload)
    {


    }

//    public static function getDataByHash($hash)
//    {
//        $client = new Client(['base_uri' => 'My ELASTIC SEARCH URL]);
//
//        //here will be 30 requests on 30 different indexes
//        $promises = [
//            'data1' => $client->getAsync('/index01/_search?q=hash:'.$hash),
//            'data2'   => $client->getAsync('/index02/_search?q=hash:'.$hash),
//            'data3'  => $client->getAsync('/index03/_search?q=hash:'.$hash),
//            'data4'  => $client->getAsync('/index04/_search?q=hash:'.$hash)
//        ];
//
//        $responses = Promise\settle($promises)->wait();
//        $convertToJson = json_decode($responses['data1']['value']->getBody());
//        $convertToJson = json_encode($convertToJson);
//
//        echo $convertToJson;
//    }




}
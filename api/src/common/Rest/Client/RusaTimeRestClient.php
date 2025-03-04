<?php

namespace App\common\Rest\Client;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Psr\Http\Message\ResponseInterface;

/**
 * REST client for the RUSA Time API
 * 
 * @package App\common\Rest\Client
 */
class RusaTimeRestClient
{

    private $rusaurl;
    private $client;
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

        $promise = $this->client->requestAsync('GET', $this->rusaurl . $payload , ['headers' => ['User-agent' => 'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/104.0.0.0 Safari/537.36', 'sec-ch-ua' => 'Chromium";v="104", " Not A;Brand";v="99", "Google Chrome";v="104']]);
        return $promise->then(
            function (ResponseInterface $res) {
                if($res->getStatusCode() === 200){
                    return $res->getBody()->getContents();
                } else {
                    return "{}";
                }

            },
            function (RequestException $e) {
                 print_r($e->getMessage() . "\n");
                print_r($e->getRequest()->getMethod());
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
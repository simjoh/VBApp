<?php

namespace App\Action\Ping;


use App\common\Action\BaseAction;
use App\common\Rest\LoppserviceRestClient;
use App\Domain\Ping\Service\PingService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PingAction extends BaseAction
{
    private $pingservice;
    private $settings;
    private LoppserviceRestClient $loppserviceRestClient;


    public function __construct(PingService $pingservice, ContainerInterface $c, LoppserviceRestClient $loppserviceRestClient)
    {
        $this->pingservice = $pingservice;
        $this->settings = $c->get('settings');
        $this->loppserviceRestClient = $loppserviceRestClient;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
//        $this->loppserviceRestClient->getAsync('http://ebrevet.org/loppservice/public/api/integration/event/all')->then(
//            function ($result) {
//                if ($result['success']) {
//                    echo json_encode($result['data']);
//                } else {
//                    echo 'Error: ' . $result['error'];
//                }
//            }
//        )->wait();

        $response->getBody()->write((string)json_encode(['healthy' => $this->pingservice->ping(), 'scrap' => null]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}
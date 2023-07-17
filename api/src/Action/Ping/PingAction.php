<?php

namespace App\Action\Ping;


use App\common\Action\BaseAction;
use App\Domain\Ping\Service\PingService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PingAction extends BaseAction
{
    private $pingservice;


    public function __construct(PingService $pingservice, ContainerInterface $c)
    {
        $this->pingservice = $pingservice;
        $this->settings = $c->get('settings');
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
//        $url = $this->settings['loppserviceurl'];
//        $test = file_get_contents($url .'/api/ping');

//
//
//        $club = ['ss'];
//
//       $crawler->filter('table')->each(function ($node) use ($club) {
//           return array_push($club, $node->text());
//        });


        $response->getBody()->write((string)json_encode(['healthy' => $this->pingservice->ping(), 'scrap' => null]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}
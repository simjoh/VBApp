<?php
namespace App\Action\Ping;


use App\common\Action\BaseAction;
use App\Domain\Ping\Service\PingService;
use Goutte\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PingAction extends BaseAction
{
    private $pingservice;


    public function __construct(PingService $pingservice)
    {
        $this->pingservice = $pingservice;
    }

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

//        $client = new Client();
//
//        $crawler = $client->request('GET', 'https://www.randonneurs.se/acpkoder_files/sheet001.htm');
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
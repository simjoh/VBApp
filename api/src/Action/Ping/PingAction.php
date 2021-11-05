<?php
namespace App\Action\Ping;


use App\common\Action\BaseAction;
use App\Domain\Ping\Service\PingService;
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
        $response->getBody()->write((string)json_encode(['healthy' => $this->pingservice->ping()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}
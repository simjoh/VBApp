<?php
namespace App\Action\Ping;


use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class PingAction
{

    public function __invoke(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write((string)json_encode(['Jag mår bra' => true]));

        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}
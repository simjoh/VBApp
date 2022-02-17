<?php

namespace App\Action\Randonneur;

use App\Domain\Model\Randonneur\Service\RandonneurService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class RandonneurAction
{


    public function __construct(ContainerInterface $c, RandonneurService $randonneurService)
    {
        $this->randonneurService = $randonneurService;
    }

    public function getCheckpoint(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $startnumber = $route->getArgument('startnumber');
        $checkpointforrandoneur =  $this->randonneurService->checkpointsForRandonneur($track_uid,$startnumber);
        $response->getBody()->write(json_encode($checkpointforrandoneur));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function stamp(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function markasDNF(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function rollbackStamp(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}
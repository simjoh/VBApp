<?php

namespace App\Action\Volonteer;



use App\Domain\Model\Volonteer\Service\VolonteerService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class VolonteerAction
{
    public function __construct(ContainerInterface $c, VolonteerService $volonteerService)
    {
        $this->volonteerService = $volonteerService;
    }

    public function getCheckpoint(ServerRequestInterface $request, ResponseInterface $response){
        //Hämta den checkpoint för volontär
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getRandonneurs(ServerRequestInterface $request, ResponseInterface $response){

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        //Hämta cyklister som har elle ska passera en viss kontroll

        $response->getBody()->write(json_encode($this->volonteerService->getRandoneursForCheckpoint($track_uid, $checkpoint_uid,$request->getAttribute('currentuserUid'))));

        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function stamp(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $participant_uid = $route->getArgument('uid');
        //Stämpla in via volontär
        $this->volonteerService->stampRandonneur($track_uid,$participant_uid,$checkpoint_uid);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function markasDNF(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $participant_uid = $route->getArgument('uid');
        //Markera dnf via volontär
        $this->volonteerService->markRandonneurDnf($track_uid,$participant_uid,$checkpoint_uid);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function rollbackStamp(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $participant_uid = $route->getArgument('uid');
        // Ångra stämpling via volontär
        $this->volonteerService->rollbackRandonneurStamp($track_uid,$participant_uid,$checkpoint_uid);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


}
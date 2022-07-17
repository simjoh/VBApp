<?php

namespace App\Action\Randonneur;

use App\Domain\Model\Randonneur\Service\RandonneurService;
use App\Domain\Model\Track\Service\TrackService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class RandonneurAction
{


    public function __construct(ContainerInterface $c, RandonneurService $randonneurService, TrackService $trackService)
    {
        $this->randonneurService = $randonneurService;
        $this->trackservice = $trackService;
    }

    public function getCheckpoint(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $startnumber = $route->getArgument('startnumber');
        $checkpointforrandoneur =  $this->randonneurService->checkpointsForRandonneur($track_uid,$startnumber, $request->getAttribute('currentuserUid'));
        $response->getBody()->write(json_encode($checkpointforrandoneur));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getCheckpointPreView(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpointforrandoneur =  $this->randonneurService->previewCheckpointsForRandonneur($track_uid, $request->getAttribute('currentuserUid'));
        $response->getBody()->write(json_encode($checkpointforrandoneur));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }



    public function getTrack(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $response->getBody()->write(json_encode($this->trackservice->getTrackByTrackUid($track_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function stamp(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $startnumber = $route->getArgument('startnumber');
        $response->getBody()->write(json_encode($this->randonneurService->stampOnCheckpoint($track_uid,$checkpoint_uid, $startnumber ,$request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function markasDNF(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        //skicka tillbacka checkpoints med ny status
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $startnumber = $route->getArgument('startnumber');
        $response->getBody()->write(json_encode($this->randonneurService->markAsDnf($track_uid,$checkpoint_uid, $startnumber ,$request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function rollbackStamp(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $startnumber = $route->getArgument('startnumber');
        //skicka tillbacka checkpoints med ny status
        $response->getBody()->write(json_encode($this->randonneurService->rollbackStamp($track_uid,$checkpoint_uid, $startnumber ,$request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function rollbackDNF(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        //skicka tillbacka checkpoints med ny status
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $startnumber = $route->getArgument('startnumber');
        $response->getBody()->write(json_encode($this->randonneurService->rollbackDnf($track_uid,$checkpoint_uid, $startnumber ,$request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }




}
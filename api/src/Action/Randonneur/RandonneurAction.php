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

    private $randonneurService;
    private $trackservice;

    public function __construct(ContainerInterface $c, RandonneurService $randonneurService, TrackService $trackService)
    {
        $this->randonneurService = $randonneurService;
        $this->trackservice = $trackService;
    }

    public function getCheckpoint(ServerRequestInterface $request, ResponseInterface $response)
    {
        //skicka tillbacka checkpoints med ny status

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $startnumber = $route->getArgument('startnumber');
        $checkpointforrandoneur = $this->randonneurService->checkpointsForRandonneur($track_uid, $startnumber, $request->getAttribute('currentuserUid'));
        $response->getBody()->write(json_encode($checkpointforrandoneur));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getCheckpointPreView(ServerRequestInterface $request, ResponseInterface $response)
    {
        //skicka tillbacka checkpoints med ny status
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpointforrandoneur = $this->randonneurService->previewCheckpointsForRandonneur($track_uid, $request->getAttribute('currentuserUid'));
        $response->getBody()->write(json_encode($checkpointforrandoneur));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function getTrack(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $response->getBody()->write(json_encode($this->trackservice->getTrackByTrackUid($track_uid, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getTrackOnly(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $startnumber = $route->getArgument('startnumber');
        $response->getBody()->write(json_encode($this->trackservice->getTrackOnlyByTrackUid($track_uid, $startnumber)));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getParticipantState(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $startnumber = $route->getArgument('startnumber');
        
        $state = $this->randonneurService->getParticipantState($track_uid, $startnumber, $request->getAttribute('currentuserUid'));
        
        if ($state === null) {
            $response->getBody()->write(json_encode(['error' => 'Participant not found']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        
        $response->getBody()->write(json_encode($state));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function stamp(ServerRequestInterface $request, ResponseInterface $response)
    {
        //skicka tillbacka checkpoints med ny status
        $latitude = $request->getQueryParams('lat')['lat'];
        $longitude = $request->getQueryParams('lat')['long'];


        if (!isset($latitude)) {
            $latitude = null;
        }

        if (!isset($longitude)) {
            $longitude = null;
        }

        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $startnumber = $route->getArgument('startnumber');
        $response->getBody()->write(json_encode($this->randonneurService->stampOnCheckpoint($track_uid, $checkpoint_uid, $startnumber, $request->getAttribute('currentuserUid'), $latitude, $longitude)));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function checkoutFrom(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $startnumber = $route->getArgument('startnumber');
        $response->getBody()->write(json_encode($this->randonneurService->checkoutFromCheckpoint($track_uid, $checkpoint_uid, $startnumber, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


    function distance($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;
        }
    }

    public function markasDNF(ServerRequestInterface $request, ResponseInterface $response)
    {
        //skicka tillbacka checkpoints med ny status
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $startnumber = $route->getArgument('startnumber');
        $response->getBody()->write(json_encode($this->randonneurService->markAsDnf($track_uid, $checkpoint_uid, $startnumber, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function rollbackStamp(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $startnumber = $route->getArgument('startnumber');
        //skicka tillbacka checkpoints med ny status
        $response->getBody()->write(json_encode($this->randonneurService->rollbackStamp($track_uid, $checkpoint_uid, $startnumber, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function undocheckoutFrom(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $startnumber = $route->getArgument('startnumber');
        $response->getBody()->write(json_encode($this->randonneurService->undoCheckoutFrom($track_uid, $checkpoint_uid, $startnumber, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function rollbackDNF(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('track_uid');
        $checkpoint_uid = $route->getArgument('checkpointUid');
        $startnumber = $route->getArgument('startnumber');
        $response->getBody()->write(json_encode($this->randonneurService->rollbackDnf($track_uid, $checkpoint_uid, $startnumber, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
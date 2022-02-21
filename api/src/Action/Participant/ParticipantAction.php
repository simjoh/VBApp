<?php

namespace App\Action\Participant;

use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Model\Partisipant\Rest\EventRepresentationTransformer;
use App\Domain\Model\Partisipant\Service\ParticipantService;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class participantAction
{

    public function __construct(ContainerInterface $c, ParticipantService $participantService)
    {
        $this->participantService = $participantService;
    }

    public function participantOnEvent(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $event_uid = $route->getArgument('eventUid');
        $response->getBody()->write(json_encode($this->participantService->participantOnEvent($event_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function participantsOnTrack(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $response->getBody()->write(json_encode($this->participantService->participantsOnTrack($track_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function participantOnTrack(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $part_uid = $route->getArgument('uid');
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function participantOnEventAndTrack(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $track_uid = $route->getArgument('trackUid');
        $event_uid = $route->getArgument('eventUid');
        $response->getBody()->write(json_encode($this->participantService->participantOnEventAndTrack($event_uid ,$track_uid, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateParticipant(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new EventRepresentationTransformer());
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function addParticipantOntrack(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new EventRepresentationTransformer());
        $checkpoint = $jsonDecoder->decode($request->getBody()->getContents(), EventRepresentation::class);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    public function uploadParticipants(ServerRequestInterface $request, ResponseInterface $response){
        $csv = array_map('str_getcsv', file('data.csv'));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    public function deleteParticipant(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


}
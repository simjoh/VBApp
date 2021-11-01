<?php

namespace App\Action\Event;

use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Model\Event\Rest\EventRepresentationTransformer;
use App\Domain\Model\Event\Service\EventService;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class EventAction
{



    public function __construct(ContainerInterface $c, EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function allEvents(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write(json_encode($this->eventService->allEvents()));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function eventFor(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $event_uid = $route->getArgument('eventUid');
        $response->getBody()->write(json_encode($this->eventService->eventFor($event_uid)));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function updateEvent(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $event_uid = $route->getArgument('eventUid');
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new EventRepresentationTransformer());
        $checkpoint = $jsonDecoder->decode($request->getBody()->getContents(), EventRepresentation::class);
        $response->getBody()->write(json_encode($this->eventService->updateEvent($event_uid,$checkpoint)));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function createEvent(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new EventRepresentationTransformer());
        $checkpoint = $jsonDecoder->decode($request->getBody()->getContents(), EventRepresentation::class);
        $response->getBody()->write(json_encode($this->eventService->createEvent($checkpoint)));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function deleteEvent(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->eventService->deleteEvent($route->getArgument('eventUid'));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }







}
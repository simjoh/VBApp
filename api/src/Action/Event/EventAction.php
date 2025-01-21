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

    private $eventService;

    public function __construct(ContainerInterface $c, EventService $eventService)
    {
        $this->eventService = $eventService;
    }

    public function allEvents(ServerRequestInterface $request, ResponseInterface $response)
    {

        $allEvents = $this->eventService->allEvents($request->getAttribute('currentuserUid'));

        if (empty($allEvents)) {
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write(json_encode($allEvents));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function eventFor(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $event_uid = $route->getArgument('eventUid');

        $event = $this->eventService->eventFor($event_uid, $request->getAttribute('currentuserUid'));

        if (!isset($event)) {
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write(json_encode($event));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function eventInformation(ServerRequestInterface $request, ResponseInterface $response)
    {

        $params = $request->getQueryParams();


        if (array_key_exists('eventUid', $params)) {
            $eventUid = $params["eventUid"];
        } else {
            $eventUid = "";
        }

        if (array_key_exists('year', $params)) {
            $year = $params["year"];
        } else {
            $year = "";
        }

        $response->getBody()->write(json_encode($this->eventService->eventInformation($eventUid, $request->getAttribute('currentuserUid')), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function updateEvent(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $event_uid = $route->getArgument('eventUid');
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new EventRepresentationTransformer());
        $checkpoint = $jsonDecoder->decode($request->getBody()->getContents(), EventRepresentation::class);
        $response->getBody()->write(json_encode($this->eventService->updateEvent($event_uid, $checkpoint, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function createEvent(ServerRequestInterface $request, ResponseInterface $response)
    {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new EventRepresentationTransformer());
        $checkpoint = $jsonDecoder->decode($request->getBody()->getContents(), EventRepresentation::class);
        $response->getBody()->write(json_encode($this->eventService->createEvent($checkpoint, $request->getAttribute('currentuserUid'))));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function deleteEvent(ServerRequestInterface $request, ResponseInterface $response)
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->eventService->deleteEvent($route->getArgument('eventUid'), $request->getAttribute('currentuserUid'));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


}
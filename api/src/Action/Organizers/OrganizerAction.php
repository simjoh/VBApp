<?php

namespace App\Action\Organizers;

use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Model\Event\Rest\EventRepresentationTransformer;
use App\Domain\Model\Organizer\Rest\OrganizerRepresentation;
use App\Domain\Model\Organizer\Rest\OrganizerRepresentationTransformer;
use App\Domain\Model\Organizer\Service\OrganizerService;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class OrganizerAction
{

    private OrganizerService $organizerService;

    public function __construct(ContainerInterface $c, OrganizerService $organizerService){

        $this->organizerService = $organizerService;
    }

    public function allOrganizers(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $response->getBody()->write(json_encode($this->organizerService->allOrganizers(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }



    public function createOrganizer(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();


        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new OrganizerRepresentationTransformer());
        $checkpoint = $jsonDecoder->decode($request->getBody()->getContents(), OrganizerRepresentation::class);
        $response->getBody()->write(json_encode($this->organizerService->createOrganizer($checkpoint, $request->getAttribute('currentuserUid'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function getOrganizer(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $organizer = $this->organizerService->organizer($route->getArgument('organizerID'));

        if(!isset($organizer)){
            return  $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write((string)json_encode($organizer));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }




}
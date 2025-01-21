<?php

namespace App\Action\Organizers;

use App\Domain\Model\Organizer\Service\OrganizerService;
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
//        $response->getBody()->write(json_encode($this->organizerService->allOrganizers(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
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
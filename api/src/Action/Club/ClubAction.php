<?php

namespace App\Action\Club;

use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class ClubAction
{

    public function __construct(){}


    public function allClubs(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}
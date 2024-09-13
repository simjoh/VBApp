<?php

namespace App\Action\Club;

use App\Domain\Model\Club\Rest\ClubRepresentation;
use App\Domain\Model\Club\Rest\ClubRepresentationTransformer;
use App\Domain\Model\Club\Service\ClubService;
use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Model\Event\Rest\EventRepresentationTransformer;
use GuzzleHttp\Client;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class ClubAction
{

    private $clubservice;

    public function __construct(ContainerInterface $c, ClubService $clubService){

        $this->clubservice = $clubService;
    }

    public function allClubs(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $response->getBody()->write(json_encode($this->clubservice->getAllClubs($request->getAttribute('currentuserUid')), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function createClub(ServerRequestInterface $request, ResponseInterface $response){


        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new ClubRepresentationTransformer());
        $club = $jsonDecoder->decode($request->getBody()->getContents(), ClubRepresentation::class);

        $response->getBody()->write(json_encode($this->clubservice->createClub($request->getAttribute('currentuserUid'), $club, ), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);




        $response->getBody()->write(json_encode($this->clubservice->createClub(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }




}
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
        $data = $request->getParsedBody();
        $club = new ClubRepresentation();
        $club->setTitle($data['title'] ?? null);
        $club->setAcpKod($data['acp_kod'] ?? null);

        $response->getBody()->write(json_encode($this->clubservice->createClub($request->getAttribute('currentuserUid'), $club), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function getClubByUid(ServerRequestInterface $request, ResponseInterface $response, array $args){
        $clubUid = $args['clubUid'];
        $response->getBody()->write(json_encode($this->clubservice->getClubByUid($clubUid, $request->getAttribute('currentuserUid')), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateClub(ServerRequestInterface $request, ResponseInterface $response, array $args){
        $clubUid = $args['clubUid'];
        $data = $request->getParsedBody();

        $club = new ClubRepresentation();
        $club->setClubUid($clubUid);
        $club->setTitle($data['title'] ?? null);
        $club->setAcpKod($data['acp_kod'] ?? null);

        $result = $this->clubservice->updateClub($request->getAttribute('currentuserUid'), $club);
        $response->getBody()->write(json_encode($result, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function deleteClub(ServerRequestInterface $request, ResponseInterface $response, array $args){
        $clubUid = $args['clubUid'];
        $response->getBody()->write(json_encode($this->clubservice->deleteClub($request->getAttribute('currentuserUid'), $clubUid), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}
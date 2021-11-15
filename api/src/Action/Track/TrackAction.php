<?php

namespace App\Action\Track;

use App\Domain\Model\Track\Rest\TrackRepresentation;
use App\Domain\Model\Track\Rest\TrackRepresentationTransformer;
use App\Domain\Model\Track\Service\TrackService;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class TrackAction
{

    private TrackService $trackService;

    public function __construct(ContainerInterface $c, TrackService $trackService)
    {
        $this->trackService = $trackService;
    }

    public function allTracks(ServerRequestInterface $request, ResponseInterface $response){
      $currentuserUid = $request->getAttribute('currentuserUid');
        $response->getBody()->write((string)json_encode( $this->trackService->allTracks($currentuserUid)), JSON_UNESCAPED_SLASHES);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function track(ServerRequestInterface $request, ResponseInterface $response){
        $currentuserUid = $request->getAttribute('currentuserUid');
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $response->getBody()->write((string)json_encode( $this->trackService->getTrackByTrackUid($route->getArgument('trackUid'),$currentuserUid)));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateTrack(ServerRequestInterface $request, ResponseInterface $response){

        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new TrackRepresentationTransformer());
        $trackrepresentation = (object) $jsonDecoder->decode($request->getBody(), TrackRepresentation::class);
        $updatedTrack = $this->trackService->updateTrack($trackrepresentation, $request->getAttribute('currentuserUid'));
        $response->getBody()->write((string)json_encode($updatedTrack),JSON_UNESCAPED_SLASHES);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function createTrack(ServerRequestInterface $request, ResponseInterface $response){
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new TrackRepresentationTransformer());
        $trackrepresentation = (object) $jsonDecoder->decode($request->getBody(), TrackRepresentation::class);
        $created = $this->trackService->createTrack($trackrepresentation,  $request->getAttribute('currentuserUid'));
        $response->getBody()->write((string)json_encode($created),JSON_UNESCAPED_SLASHES);
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


}
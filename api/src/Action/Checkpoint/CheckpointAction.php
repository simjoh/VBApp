<?php

namespace App\Action\CheckPoint;


use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentationTranformer;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

class CheckpointAction
{
    private CheckpointsService $checkpointsService;

    public function __construct(ContainerInterface $c, CheckpointsService $checkpointsService)
    {
        $this->checkpointsService = $checkpointsService;
    }

    public function allCheckpoints(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write(json_encode($this->checkpointsService->allCheckpoints()));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function checkpointFor(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $response->getBody()->write(json_encode($this->checkpointsService->checkpointFor($route->getArgument('checkpointUID'))));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateCheckpoint(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $checkpoint_uid = $route->getArgument('checkpointUID');
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new CheckpointRepresentationTranformer());
        $checkpoint =  $jsonDecoder->decode($request->getBody(), CheckpointRepresentation::class);
        $response->getBody()->write(json_encode($this->checkpointsService->updateCheckpoint($checkpoint_uid,$checkpoint)));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function createCheckpoint(ServerRequestInterface $request, ResponseInterface $response){
        // Read the request body once
        $body = $request->getBody()->getContents();
        $data = json_decode($body, true);
        
        // Get track_uid from request body
        $track_uid = $data['track_uid'] ?? null;
        if (!$track_uid) {
            throw new \InvalidArgumentException('track_uid is required in request body');
        }
        
        // Decode the checkpoint data
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new CheckpointRepresentationTranformer());
        $checkpoint = $jsonDecoder->decode($body, CheckpointRepresentation::class);
        
        $response->getBody()->write(json_encode($this->checkpointsService->createCheckpoint($track_uid, $checkpoint)));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function upload(ServerRequestInterface $request, ResponseInterface $response){
        $files = $request->getUploadedFiles();
        $response->getBody()->write((string)json_encode(['ladda upp bild' => true]));
    }
    public function deleteCheckpoint(ServerRequestInterface $request, ResponseInterface $response){
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->checkpointsService->deleteCheckpoint($route->getArgument('checkpointUID'));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }









}
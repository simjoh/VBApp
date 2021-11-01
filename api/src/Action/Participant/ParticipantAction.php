<?php

namespace App\Action\Participant;

use App\Domain\Model\Event\Service\EventService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class participantAction
{

    public function __construct(ContainerInterface $c)
    {

    }

    public function participantOnEvent(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function participantsOnTrack(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function participantOnTrack(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function updateParticipant(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
    public function addParticipantOntrack(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    public function uploadParticipants(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }
    public function deleteParticipant(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


}
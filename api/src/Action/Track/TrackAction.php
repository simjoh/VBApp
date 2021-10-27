<?php

namespace App\Action\Track;

use App\Domain\Model\Site\Service\SiteService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class TrackAction
{



    public function __construct(ContainerInterface $c)
    {

    }

    public function allTracks(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function track(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateTrack(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function createTrack(ServerRequestInterface $request, ResponseInterface $response){
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }


}
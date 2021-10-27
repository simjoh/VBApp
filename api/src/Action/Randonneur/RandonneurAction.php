<?php

namespace App\Action\Randonneur;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class RandonneurAction
{


    public function __construct(ContainerInterface $c)
    {

    }

    public function getCheckpoints(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka ccheckpints med ny status
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function stamp(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function markasDNF(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function rollbackStamp(ServerRequestInterface $request, ResponseInterface $response){
        //skicka tillbacka checkpoints med ny status
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

}
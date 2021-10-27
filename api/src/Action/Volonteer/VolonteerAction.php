<?php

namespace App\Action\Volonteer;

use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class VolonteerAction
{
    public function __construct(ContainerInterface $c)
    {

    }

    public function getCheckpoint(ServerRequestInterface $request, ResponseInterface $response){
        //Hämta den checkpoint för volontär
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getRandonneurs(ServerRequestInterface $request, ResponseInterface $response){
        //Hämta cyklister som har elle ska passera en viss kontroll
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


    public function stamp(ServerRequestInterface $request, ResponseInterface $response){
        //Stämpla in via volontär
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function markasDNF(ServerRequestInterface $request, ResponseInterface $response){
        //Markera dnf via volontär
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function rollbackStamp(ServerRequestInterface $request, ResponseInterface $response){
        // Ångra stämpling via volontär
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


}
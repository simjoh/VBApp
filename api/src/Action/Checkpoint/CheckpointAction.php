<?php

namespace App\Action\Control;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class CheckpointAction
{

    public function __construct(ContainerInterface $c)
    {

    }

    public function allCheckpoints(ServerRequestInterface $request, ResponseInterface $response){
                $response->getBody()->write((string)json_encode(['Alla kontroller' => true]));
    }

    public function checkpointFor(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write((string)json_encode(['Kontroll för uid' => true]));
    }

    public function updateCheckpoint(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write((string)json_encode(['Updatater Kontroll för uid' => true]));
    }
    public function createControl(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write((string)json_encode(['skapaKontroll' => true]));
    }

    public function upload(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write((string)json_encode(['ladda upp bild' => true]));
    }
    public function deleteCheckpoint(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write((string)json_encode(['tabortKontroll' => true]));
    }









}
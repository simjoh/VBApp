<?php

namespace App\Action\Control;


use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ControlAction
{

    public function __construct(ContainerInterface $c)
    {

    }

    public function allControls(ServerRequestInterface $request, ResponseInterface $response){
                $response->getBody()->write((string)json_encode(['Alla kontroller' => true]));
    }

    public function controlFor(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write((string)json_encode(['Kontroll för uid' => true]));
    }

    public function updateControl(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write((string)json_encode(['Updatater Kontroll för uid' => true]));
    }
    public function createControl(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write((string)json_encode(['skapaKontroll' => true]));
    }

    public function upload(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write((string)json_encode(['ladda upp bild' => true]));
    }
    public function deleteControl(ServerRequestInterface $request, ResponseInterface $response){
        $response->getBody()->write((string)json_encode(['tabortKontroll' => true]));
    }









}
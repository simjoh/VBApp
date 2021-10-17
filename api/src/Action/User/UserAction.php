<?php

namespace App\Action\User;

use App\common\Action\BaseAction;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserAction extends BaseAction
{

    public function allUsers(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write("allUsers");
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getUserById(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write("user");
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write("updateUSer");
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function newUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write("New user");
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function deleteUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $response->getBody()->write("Delete");
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }


}
<?php

namespace App\Action\User;

use App\common\Action\BaseAction;
use App\common\CleanJsonSerializer;
use App\Domain\Authenticate\Service\AuthenticationService;
use App\Domain\Model\User\Service\UserService;
use JMS\Serializer\SerializerBuilder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class UserAction extends BaseAction
{

    private $key;
    private $userservice;

    public function __construct(ContainerInterface $c, UserService $userService)
    {
        $this->userservice = $userService;
    }

    public function allUsers(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $allUsers = $this->userservice->getAllUsers();
        $ser = new CleanJsonSerializer();
        $response->getBody()->write($ser->serialize($allUsers));
       return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);


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
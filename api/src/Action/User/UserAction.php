<?php

namespace App\Action\User;

use App\common\Action\BaseAction;
use App\common\CleanJsonSerializer;
use App\Domain\Authenticate\Service\AuthenticationService;
use App\Domain\Model\User\Service\UserService;
use App\Domain\Model\User\User;
use Exception;
use JMS\Serializer\SerializerBuilder;
use Karriere\JsonDecoder\JsonDecoder;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Slim\Routing\RouteContext;

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
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $user = $this->userservice->getUserById($route->getArgument('id'));

        $seriializer = new CleanJsonSerializer();
        $response->getBody()->write($seriializer->serialize($user));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->scanAndRegister(User::class);
        $userParsed = $jsonDecoder->decode($request->getBody(), User::class);
        $userUpdated = $this->userservice->updateUser($route->getArgument('id'), $userParsed);
        $seriializer = new CleanJsonSerializer();
        $response->getBody()->write($seriializer->serialize($userUpdated));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function createUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->scanAndRegister(User::class);
        $user  =  $jsonDecoder->decode($request->getBody(), User::class);
        $this->userservice->createUser($user);
        $seriializer = new CleanJsonSerializer();
        $response->getBody()->write($seriializer->serialize($user));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function deleteUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->userservice->deleteUser($route->getArgument('id'));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }







}
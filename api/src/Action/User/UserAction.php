<?php

namespace App\Action\User;

use App\common\Action\BaseAction;
use App\common\CleanJsonSerializer;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Model\Site\Rest\SiteRepresentationTransformer;
use App\Domain\Model\User\Rest\UserRepresentation;
use App\Domain\Model\User\Rest\UserRepresentationTransformer;
use App\Domain\Model\User\Service\UserService;
use App\Domain\Model\User\User;
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
        if (empty($allUsers)) {
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write(json_encode($allUsers));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getUserById(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $user = $this->userservice->getUserById($route->getArgument('id'));
        if (!isset($user)) {
            return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write((string)json_encode($user));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function updateUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new UserRepresentationTransformer());
        $userParsed = $jsonDecoder->decode($request->getBody(), UserRepresentation::class);
        $userUpdated = $this->userservice->updateUser($route->getArgument('id'), $userParsed);
        $response->getBody()->write((string)json_encode($userUpdated));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function createUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new UserRepresentationTransformer());
        $userrepresentation = $jsonDecoder->decode($request->getBody(), UserRepresentation::class);
        $newUser = $this->userservice->createUser($userrepresentation);
        $response->getBody()->write((string)json_encode($newUser));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function deleteUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->userservice->deleteUser($route->getArgument('id'));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
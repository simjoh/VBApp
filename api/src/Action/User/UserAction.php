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
        $currentUserUIDInSystem = $request->getAttribute('currentuserUid');
        $allUsers = $this->userservice->getAllUsers($currentUserUIDInSystem);
        if(empty($allUsers)){
            return  $response->withHeader('Content-Type', 'application/json')->withStatus(404);
        }
        $response->getBody()->write(json_encode($allUsers));
        return  $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function getUserById(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $user = $this->userservice->getUserById($route->getArgument('id'), $request->getAttribute('currentuserUid'));
        if(!isset($user)){
            return  $response->withHeader('Content-Type', 'application/json')->withStatus(404);
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
        $userrepresentation = $jsonDecoder->decode($request->getBody(), UserRepresentation::class);
        $userUpdated = $this->userservice->updateUser($route->getArgument('id'), $userrepresentation, $request->getAttribute('currentuserUid'));
        $response->getBody()->write((string)json_encode($userUpdated));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }

    public function createUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {

        $currentUserUIDInSystem = $request->getAttribute('currentuserUid');
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->register(new UserRepresentationTransformer());
        $userrepresentation  =  $jsonDecoder->decode($request->getBody(), UserRepresentation::class);
        $newUser = $this->userservice->createUser($userrepresentation, $currentUserUIDInSystem);
        $response->getBody()->write((string)json_encode($newUser));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
    }

    public function deleteUser(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $routeContext = RouteContext::fromRequest($request);
        $route = $routeContext->getRoute();
        $this->userservice->deleteUser($route->getArgument('id'));
        return $response->withStatus(204);
    }

    public function getAvailableRoles(ServerRequestInterface $request, ResponseInterface $response): ResponseInterface
    {
        $userContext = \App\common\Context\UserContext::getInstance();
        
        $availableRoles = [];
        
        if ($userContext->isSuperUser()) {
            // Superusers can assign all roles
            $availableRoles = [
                ['id' => 3, 'role_name' => 'SUPERUSER', 'description' => 'Superanvändare med alla behörigheter'],
                ['id' => 1, 'role_name' => 'ADMIN', 'description' => 'Administratör med skriv och läsrättigheter'],
                ['id' => 2, 'role_name' => 'USER', 'description' => 'Läsbehörighet och viss skrivbehörighet'],
                ['id' => 6, 'role_name' => 'VOLONTEER', 'description' => 'Behörighet att checka in och se passeringar vid kontroller'],
                ['id' => 5, 'role_name' => 'DEVELOPER', 'description' => 'Specialbehörighet för utvecklare']
            ];
        } else {
            // Non-superusers can only assign these roles
            $availableRoles = [
                ['id' => 1, 'role_name' => 'ADMIN', 'description' => 'Administratör med skriv och läsrättigheter'],
                ['id' => 2, 'role_name' => 'USER', 'description' => 'Läsbehörighet och viss skrivbehörighet'],
                ['id' => 6, 'role_name' => 'VOLONTEER', 'description' => 'Behörighet att checka in och se passeringar vid kontroller']
            ];
        }
        
        $response->getBody()->write(json_encode($availableRoles));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(200);
    }
}
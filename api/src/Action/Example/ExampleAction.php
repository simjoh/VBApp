<?php

namespace App\Action\Example;

use App\common\Context\UserContext;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ExampleAction
{
    public function __invoke(Request $request, Response $response): Response
    {
        // Get the UserContext instance
        $context = UserContext::getInstance();

        // Example of using context to check permissions
        if (!$context->isAdmin() && !$context->isVolonteer()) {
            $response->getBody()->write(json_encode([
                'error' => 'Unauthorized - Admin or Volonteer access required'
            ]));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        // Example of using organizer context
        $organizerId = $context->getOrganizerId();
        if ($organizerId === null && !$context->isAdmin()) {
            $response->getBody()->write(json_encode([
                'error' => 'No organization associated with user'
            ]));
            return $response->withStatus(403)->withHeader('Content-Type', 'application/json');
        }

        // Example response with user context information
        $data = [
            'userId' => $context->getUserId(),
            'organizerId' => $organizerId,
            'roles' => $context->getRoles(),
            'permissions' => $context->getPermissions(),
            'isAdmin' => $context->isAdmin(),
            'isVolonteer' => $context->isVolonteer()
        ];

        $response->getBody()->write(json_encode($data));
        return $response->withHeader('Content-Type', 'application/json');
    }
} 
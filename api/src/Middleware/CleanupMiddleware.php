<?php

namespace App\Middleware;

use App\common\CurrentOrganizer;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use App\Helpers\CurrentUser;

class CleanupMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        // Process the request and get the response
        $response = $handler->handle($request);
        CurrentOrganizer::setUser(null);
        return $response;
    }
}
<?php

namespace App\Middleware;

use App\common\CurrentUser;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface;
use Psr\Http\Message\ResponseInterface as Response;

class CleanupUserMiddleware
{
    public function __invoke(Request $request, RequestHandlerInterface $handler): Response
    {
        // Process the request and get the response
        $response = $handler->handle($request);
        CurrentUser::setUser(null);
        return $response;
    }
}
<?php

namespace App\Middleware;

use App\common\Context\UserContext;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class UserContextCleanupMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        try {
            // Handle the request
            $response = $handler->handle($request);
            
            // Clean up the context
            UserContext::getInstance()->clear();
            
            return $response;
        } catch (\Throwable $e) {
            // Ensure cleanup even if an exception occurs
            UserContext::getInstance()->clear();
            throw $e;
        }
    }
} 
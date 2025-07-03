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
            error_log("[UserContextCleanup] Starting request handling");
            
            // Handle the request
            $response = $handler->handle($request);
            
            // Clean up the context
            UserContext::getInstance()->clear();
            error_log("[UserContextCleanup] Context cleared successfully after request");
            
            return $response;
        } catch (\Throwable $e) {
            // Ensure cleanup even if an exception occurs
            error_log("[UserContextCleanup] Error during request: " . $e->getMessage());
            UserContext::getInstance()->clear();
            error_log("[UserContextCleanup] Context cleared after error");
            throw $e;
        }
    }
} 
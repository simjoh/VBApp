<?php

namespace App\Middleware;

use App\common\Context\UserContext;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Parser;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class UserContextMiddleware
{
    private $key;

    public function __construct(ContainerInterface $c)
    {
        $this->key = $c->get('settings')['secretkey'];
    }

    public function __invoke(Request $request, RequestHandler $handler): ResponseInterface
    {
        $userAgent = $request->getHeaderLine("User-Agent");
        
        if ($userAgent === 'Loppservice/1.0') {
            return $handler->handle($request);
        }

        // Get token from either TOKEN or Authorization header
        $token = $request->getHeaderLine("TOKEN");
        if (empty($token)) {
            $authHeader = $request->getHeaderLine("Authorization");
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }

        if (!empty($token)) {
            try {
                $signer = new HS256($this->key);
                $parser = new Parser($signer);
                $claims = $parser->parse($token);

                // Extract organizer_id safely
                $organizerId = isset($claims['organizer_id']) ? $claims['organizer_id'] : null;
                $roles = isset($claims['roles']) ? $claims['roles'] : [];

                // Initialize UserContext
                UserContext::getInstance()->initialize($claims['id'], $organizerId, $roles);

                // Handle the request
                $response = $handler->handle($request);

                // Clear context after request is handled
                UserContext::getInstance()->clear();

                return $response;
            } catch (\Exception $e) {
                UserContext::getInstance()->clear();
                throw $e;
            }
        }
        return $handler->handle($request);
    }

    private function normalizeRoles(array $roles): array
    {
        $normalizedRoles = [];
        foreach ($roles as $key => $value) {
            if (is_string($key) && $value === true) {
                // Convert from format like ['isAdmin' => true] to ['ADMIN']
                $role = strtoupper(substr($key, 2)); // Remove 'is' prefix
                $normalizedRoles[] = $role;
            } elseif (is_string($value)) {
                // Handle direct role strings
                $normalizedRoles[] = $value;
            }
        }
        return $normalizedRoles;
    }
} 
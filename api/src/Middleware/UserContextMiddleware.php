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
            error_log("[UserContext] Skipping context for Loppservice request");
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
                error_log("[UserContext] Processing token for request: " . $request->getUri()->getPath());
                
                $signer = new HS256($this->key);
                $parser = new Parser($signer);
                $claims = $parser->parse($token);

                // Extract organizer_id safely
                $organizerId = isset($claims['organizer_id']) ? $claims['organizer_id'] : null;
                $roles = isset($claims['roles']) ? $claims['roles'] : [];

                error_log("[UserContext] Token claims: " . json_encode([
                    'user_id' => $claims['id'],
                    'organizer_id' => $organizerId,
                    'roles' => $roles
                ]));

                // Initialize UserContext
                UserContext::getInstance()->initialize($claims['id'], $organizerId, $roles);
                
                // Log the entire UserContext object for verification
                $context = UserContext::getInstance();
                error_log("[UserContext] Full UserContext object: " . json_encode([
                    'userId' => $context->getUserId(),
                    'organizerId' => $context->getOrganizerId(),
                    'hasOrganization' => $context->hasOrganization(),
                    'roles' => $context->getRoles(),
                    'isAdmin' => $context->isAdmin(),
                    'isVolonteer' => $context->isVolonteer(),
                    'isCompetitor' => $context->isCompetitor(),
                    'isSuperUser' => $context->isSuperUser(),
                    'isDeveloper' => $context->isDeveloper()
                ]));

                error_log("[UserContext] Context initialized successfully");

                // Handle the request
                $response = $handler->handle($request);

                // Clear context after request is handled
                UserContext::getInstance()->clear();
                error_log("[UserContext] Context cleared after request");

                return $response;
            } catch (\Exception $e) {
                error_log("[UserContext] Error processing token: " . $e->getMessage());
                UserContext::getInstance()->clear();
                throw $e;
            }
        }

        error_log("[UserContext] No token found, proceeding without context");
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
        error_log("[UserContext] Normalized roles: " . json_encode($normalizedRoles));
        return $normalizedRoles;
    }
} 
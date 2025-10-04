<?php

namespace App\Http\Middleware;

use App\Context\UserContext;
use Closure;
use Illuminate\Http\Request;

class JwtUserContextMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if this is an internal service call that should bypass JWT validation
        $userAgent = $request->header('User-Agent');
        if ($userAgent === config('jwt.bypass_user_agent')) {
            // For bypass requests, clear any existing context and continue
            UserContext::getInstance()->clear();
            return $next($request);
        }

        // Get JWT claims from the request (set by JwtTokenValidatorMiddleware)
        $claims = $request->attributes->get('jwt_claims');

        if ($claims) {
            // Extract user information from JWT claims
            $userId = $claims['id'] ?? null;
            $organizerId = $claims['organizer_id'] ?? null;
            $roles = $claims['roles'] ?? [];
            $permissions = $claims['permissions'] ?? [];

            // Normalize roles (handle both array and object formats)
            $normalizedRoles = $this->normalizeRoles($roles);

            // Initialize UserContext
            if ($userId) {
                UserContext::getInstance()->initialize($userId, $organizerId, $normalizedRoles, $permissions);

                // Also store in request attributes for backward compatibility
                $request->attributes->set('current_user_id', $userId);
                $request->attributes->set('current_organizer_id', $organizerId);
                $request->attributes->set('current_user_roles', $normalizedRoles);
            }
        }

        try {
            // Handle the request
            $response = $next($request);

            // Clear context after request is handled
            UserContext::getInstance()->clear();

            return $response;
        } catch (\Throwable $e) {
            // Ensure cleanup even if an exception occurs
            UserContext::getInstance()->clear();
            throw $e;
        }
    }

    /**
     * Normalize roles from JWT claims to a consistent array format
     *
     * @param mixed $roles
     * @return array
     */
    private function normalizeRoles($roles): array
    {
        if (!is_array($roles)) {
            return [];
        }

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


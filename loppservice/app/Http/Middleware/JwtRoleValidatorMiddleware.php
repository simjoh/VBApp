<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;

class JwtRoleValidatorMiddleware
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
            return $next($request);
        }

        // Get JWT claims from the request (set by JwtTokenValidatorMiddleware)
        $claims = $request->attributes->get('jwt_claims');

        if (!$claims) {
            throw new AuthenticationException('JWT token not found or invalid');
        }

        // Extract roles from claims
        $roles = $claims['roles'] ?? [];

        // Ensure roles is an array
        if (!is_array($roles)) {
            $roles = [];
        }

        // Get valid roles from config
        $validRoles = config('jwt.valid_roles', []);

        // Check if user has any valid role
        $hasValidRole = false;
        foreach ($roles as $role) {
            if (in_array($role, $validRoles)) {
                $hasValidRole = true;
                break;
            }
        }

        if (!$hasValidRole) {
            throw new AuthenticationException('Insufficient permissions - valid role required');
        }

        // Store user context in request for use by controllers
        $request->attributes->set('jwt_user_id', $claims['id'] ?? null);
        $request->attributes->set('jwt_organizer_id', $claims['organizer_id'] ?? null);
        $request->attributes->set('jwt_roles', $roles);

        return $next($request);
    }
}


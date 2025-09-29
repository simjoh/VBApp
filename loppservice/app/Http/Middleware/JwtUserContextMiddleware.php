<?php

namespace App\Http\Middleware;

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
            return $next($request);
        }

        // Get JWT context from request attributes (set by previous middleware)
        $userId = $request->attributes->get('jwt_user_id');
        $organizerId = $request->attributes->get('jwt_organizer_id');
        $roles = $request->attributes->get('jwt_roles', []);

        // Store user context in request for easy access in controllers
        if ($userId) {
            $request->attributes->set('current_user_id', $userId);
            $request->attributes->set('current_organizer_id', $organizerId);
            $request->attributes->set('current_user_roles', $roles);
        }

        return $next($request);
    }
}


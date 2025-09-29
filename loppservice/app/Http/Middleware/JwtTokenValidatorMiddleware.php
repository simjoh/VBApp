<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use MiladRahimi\Jwt\Cryptography\Algorithms\Hmac\HS256;
use MiladRahimi\Jwt\Exceptions\ValidationException;
use MiladRahimi\Jwt\Parser;
use MiladRahimi\Jwt\Validator\DefaultValidator;
use MiladRahimi\Jwt\Validator\Rules\NewerThanOrSame;
use MiladRahimi\Jwt\Validator\Rule;

class JwtTokenValidatorMiddleware
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

        // Try to get token from TOKEN header first
        $token = $request->header('TOKEN');

        // If TOKEN header is empty, try Authorization header
        if (empty($token)) {
            $authHeader = $request->header('Authorization');
            if (preg_match('/Bearer\s+(.*)$/i', $authHeader, $matches)) {
                $token = $matches[1];
            }
        }

        if (empty($token)) {
            throw new AuthenticationException('JWT token is required');
        }

        try {
            // Create validator with expiration rule
            $validator = new DefaultValidator();
            $validator->addRule('exp', new NewerThanOrSame(time()), true);

            // Create signer and parser
            $signer = new HS256(config('jwt.secret'));
            $parser = new Parser($signer, $validator);

            // Parse and validate the token
            $claims = $parser->parse($token);

            // Store the parsed claims in the request for use by other middleware
            $request->attributes->set('jwt_claims', $claims);

        } catch (ValidationException $e) {
            throw new AuthenticationException('Invalid or expired JWT token');
        }

        return $next($request);
    }
}

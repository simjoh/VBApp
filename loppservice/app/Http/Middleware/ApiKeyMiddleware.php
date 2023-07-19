<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;

class ApiKeyMiddleware
{


    protected $apikeyInterface;

    function __construct(Request $categoryParam)
    {

    }

    public function handle(Request $request, Closure $next)
    {
        if (app()->environment() != 'dev') {
            if (!$request->hasHeader('apikey')) {
                throw new AuthenticationException('Api key is mandatory');
            } else {
                if ($request->header('apikey') != 'testkey') {
                    throw new AuthenticationException('Wrong api key');
                }
            }
        }
        return $next($request);

    }
}

<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to your application's "home" route.
     *
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const HOME = '/home';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     */
    public function boot(): void
    {
        RateLimiter::for('loppservice', function (Request $request) {
            // For API requests, use the API key or IP address for rate limiting
            // Increase the limit to 120 requests per minute for API usage
            $identifier = $request->header('apikey') ?: $request->ip();
            return Limit::perMinute(120)->by($identifier);
        });


        $this->routes(function () {
            Route::middleware('loppservice')
                ->prefix('loppservice')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }
}

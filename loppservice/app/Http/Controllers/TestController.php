<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class TestController extends Controller
{
    public function simpleTest()
    {
        return response()->json(['message' => 'Simple test route works']);
    }

    public function pingApiKey()
    {
        return 'Testar kontroll av apinyckel';
    }

    public function pingJwt(Request $request)
    {
        return response()->json([
            'message' => 'JWT validation successful',
            'user_id' => $request->attributes->get('current_user_id'),
            'organizer_id' => $request->attributes->get('current_organizer_id'),
            'roles' => $request->attributes->get('current_user_roles', []),
            'timestamp' => now()->toISOString()
        ]);
    }

    public function testJwt()
    {
        return response()->json([
            'message' => 'Test route without JWT middleware',
            'timestamp' => now()->toISOString()
        ]);
    }

    public function migrate()
    {
        Artisan::call('migrate', ["--force" => true]);
        return response()->json(['message' => 'Migration completed successfully']);
    }

    public function countryUpdate()
    {
        Artisan::call('app:country-update');
        return response()->json(['message' => 'Country update completed successfully']);
    }

    public function cacheRun()
    {
        Artisan::call('view:cache');
        Artisan::call('route:cache');
        Artisan::call('event:cache');
        return response()->json(['message' => 'Cache commands completed successfully']);
    }

    public function scheduleRun()
    {
        Artisan::call('schedule:run');
        return response()->json(['message' => 'Schedule run completed successfully']);
    }
}

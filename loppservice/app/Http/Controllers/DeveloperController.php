<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

/**
 * Developer Controller
 *
 * Handles developer-specific tools and utilities
 * Separated from ToolController for better separation of concerns
 */
class DeveloperController extends Controller
{
    /**
     * Run database migrations
     */
    public function migrate(): JsonResponse
    {
        try {
            Artisan::call('migrate', ['--force' => true]);

            return response()->json([
                'success' => true,
                'message' => 'Migration completed successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Migration failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Migration failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run country update command
     */
    public function countryUpdate(): JsonResponse
    {
        try {
            Artisan::call('app:country-update');

            return response()->json([
                'success' => true,
                'message' => 'Country update completed successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Country update failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Country update failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run cache commands
     */
    public function cacheRun(): JsonResponse
    {
        try {
            Artisan::call('view:cache');
            Artisan::call('route:cache');
            Artisan::call('event:cache');

            return response()->json([
                'success' => true,
                'message' => 'Cache commands completed successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Cache commands failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Cache commands failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Run scheduled commands
     */
    public function scheduleRun(): JsonResponse
    {
        try {
            Artisan::call('schedule:run');

            return response()->json([
                'success' => true,
                'message' => 'Schedule run completed successfully',
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('Schedule run failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Schedule run failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test application integration
     */
    public function testAppIntegration(): JsonResponse
    {
        try {
            $response = \Illuminate\Support\Facades\Http::withHeaders([
                'APIKEY' => env('BREVET_APP_API_KEY'),
            ])->get(env("BREVET_APP_URL") . '/ping');

            return response()->json([
                'success' => true,
                'message' => 'App integration test completed',
                'response' => $response->json(),
                'timestamp' => now()->toISOString()
            ]);
        } catch (\Exception $e) {
            Log::error('App integration test failed: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'App integration test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

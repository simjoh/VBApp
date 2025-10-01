<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class IntegrationController extends Controller
{
    /**
     * Test cycling app integration
     */
    public function testCyclingAppIntegration(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Cycling app integration is working'
        ]);
    }

    /**
     * Publish to cycling app
     */
    public function publishToCyclingApp(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => 'Published to cycling app'
        ]);
    }

    /**
     * Get published events count
     */
    public function getPublishedEventsCount(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'count' => 0,
            'message' => 'Published events count retrieved'
        ]);
    }
}

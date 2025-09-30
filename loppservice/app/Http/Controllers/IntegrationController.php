<?php

namespace App\Http\Controllers;

use App\Events\CreateParticipantInCyclingAppEvent;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

/**
 * Integration Controller
 *
 * Handles external integrations and data transfer operations
 * Separated from ToolController for better separation of concerns
 */
class IntegrationController extends Controller
{
    /**
     * Publish participants to cycling app if not already registered
     */
    public function publishToCyclingApp(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'event' => 'required|string',
                'reguid' => 'nullable|string'
            ]);

            $courseUid = $request->input('event');
            $reguid = $request->input('reguid');

            if ($reguid) {
                $results = DB::table('registrations as r')
                    ->select('r.registration_uid', 'p.person_uid', 'r.course_uid')
                    ->distinct()
                    ->join('person as p', 'p.person_uid', '=', 'r.person_uid')
                    ->join('contactinformation as ci', 'ci.person_person_uid', '=', 'p.person_uid')
                    ->where('r.registration_uid', '=', $reguid)
                    ->get();
            } else {
                $results = DB::table('registrations as r')
                    ->select('r.registration_uid', 'p.person_uid', 'r.course_uid')
                    ->distinct()
                    ->join('person as p', 'p.person_uid', '=', 'r.person_uid')
                    ->join('contactinformation as ci', 'ci.person_person_uid', '=', 'p.person_uid')
                    ->join('clubs as c', 'c.club_uid', '=', 'r.club_uid')
                    ->join('adress as a', 'a.person_person_uid', '=', 'p.person_uid')
                    ->join('countries as co', 'co.country_id', '=', 'a.country_id')
                    ->where('r.course_uid', '=', $courseUid)
                    ->whereNotIn('r.registration_uid', function ($query) {
                        $query->select('registration_uid')
                            ->from('published_events');
                    })
                    ->get();
            }

            $count = 0;
            if (!$results->isEmpty()) {
                foreach ($results as $result) {
                    event(new CreateParticipantInCyclingAppEvent(
                        $result->course_uid,
                        $result->person_uid,
                        $result->registration_uid
                    ));
                    sleep(1); // Rate limiting
                }
                $count = count($results);
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully queued {$count} participants for publishing",
                'transferred_count' => $count,
                'course_uid' => $courseUid,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error publishing to cycling app: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to publish to cycling app',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get published events count
     */
    public function getPublishedEventsCount(): JsonResponse
    {
        try {
            $count = DB::table('published_events')->count();

            return response()->json([
                'success' => true,
                'count' => $count,
                'message' => "Found {$count} published events",
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting published events count: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'count' => 0,
                'message' => 'Failed to get published events count',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test integration with external cycling app
     */
    public function testCyclingAppIntegration(): JsonResponse
    {
        try {
            $response = Http::withHeaders([
                'APIKEY' => env('BREVET_APP_API_KEY'),
            ])->timeout(30)->get(env("BREVET_APP_URL") . '/ping');

            return response()->json([
                'success' => $response->successful(),
                'message' => 'Cycling app integration test completed',
                'response_status' => $response->status(),
                'response_data' => $response->json(),
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Exception $e) {
            Log::error('Cycling app integration test failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Cycling app integration test failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

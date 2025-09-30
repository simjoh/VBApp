<?php

namespace App\Http\Controllers;

use App\Events\CreateParticipantInCyclingAppEvent;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Error Event Controller
 *
 * Handles error event management and retry operations
 * Separated from ToolController for better separation of concerns
 */
class ErrorEventController extends Controller
{
    /**
     * Get error events with pagination
     */
    public function getErrorEvents(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'limit' => 'integer|min:1|max:1000',
                'offset' => 'integer|min:0'
            ]);

            $limit = $request->get('limit', 50);
            $offset = $request->get('offset', 0);

            $errorEvents = DB::table('error_events')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get();

            $totalCount = DB::table('error_events')->count();

            return response()->json([
                'success' => true,
                'data' => $errorEvents,
                'total' => $totalCount,
                'limit' => $limit,
                'offset' => $offset
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error fetching error events: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch error events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get failed publish events for retry
     */
    public function getFailedPublishEvents(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'limit' => 'integer|min:1|max:1000',
                'offset' => 'integer|min:0',
                'event_type' => 'string|in:eventregistration,eventupdate,eventdelete'
            ]);

            $limit = $request->get('limit', 50);
            $offset = $request->get('offset', 0);
            $eventType = $request->get('event_type', 'eventregistration');

            $errorEvents = DB::table('error_events')
                ->where('type', $eventType)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->offset($offset)
                ->get();

            $totalCount = DB::table('error_events')
                ->where('type', $eventType)
                ->count();

            return response()->json([
                'success' => true,
                'data' => $errorEvents,
                'total' => $totalCount,
                'limit' => $limit,
                'offset' => $offset,
                'event_type' => $eventType
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error fetching failed publish events: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch failed publish events',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retry a single failed publish event
     */
    public function retryPublishEvent(Request $request, string $errorEventUid): JsonResponse
    {
        try {
            $request->validate([
                'errorEventUid' => 'required|string|uuid'
            ]);

            // Get the error event
            $errorEvent = DB::table('error_events')
                ->where('errorevent_uid', $errorEventUid)
                ->where('type', 'eventregistration')
                ->first();

            if (!$errorEvent) {
                return response()->json([
                    'success' => false,
                    'message' => 'Error event not found or not a failed publish event'
                ], 404);
            }

            // Get registration details
            $registration = DB::table('registrations as r')
                ->select('r.registration_uid', 'p.person_uid', 'r.course_uid')
                ->join('person as p', 'p.person_uid', '=', 'r.person_uid')
                ->where('r.registration_uid', $errorEvent->registration_uid)
                ->first();

            if (!$registration) {
                return response()->json([
                    'success' => false,
                    'message' => 'Registration not found'
                ], 404);
            }

            // Trigger the event
            event(new CreateParticipantInCyclingAppEvent(
                $registration->course_uid,
                $registration->person_uid,
                $registration->registration_uid
            ));

            // Remove the error event since we're retrying
            DB::table('error_events')
                ->where('errorevent_uid', $errorEventUid)
                ->delete();

            return response()->json([
                'success' => true,
                'message' => 'Publish event retry triggered successfully',
                'data' => [
                    'error_event_uid' => $errorEventUid,
                    'registration_uid' => $registration->registration_uid,
                    'course_uid' => $registration->course_uid,
                    'person_uid' => $registration->person_uid
                ],
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error retrying publish event: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retry publish event',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Retry all failed publish events
     */
    public function retryAllPublishEvents(Request $request): JsonResponse
    {
        try {
            $request->validate([
                'event_type' => 'string|in:eventregistration,eventupdate,eventdelete',
                'limit' => 'integer|min:1|max:1000'
            ]);

            $eventType = $request->get('event_type', 'eventregistration');
            $limit = $request->get('limit', 100);

            // Get all failed publish events
            $errorEvents = DB::table('error_events')
                ->where('type', $eventType)
                ->limit($limit)
                ->get();

            if ($errorEvents->isEmpty()) {
                return response()->json([
                    'success' => true,
                    'message' => 'No failed publish events found to retry',
                    'retried_count' => 0
                ]);
            }

            $retriedCount = 0;
            $failedCount = 0;
            $errors = [];

            foreach ($errorEvents as $errorEvent) {
                try {
                    // Get registration details
                    $registration = DB::table('registrations as r')
                        ->select('r.registration_uid', 'p.person_uid', 'r.course_uid')
                        ->join('person as p', 'p.person_uid', '=', 'r.person_uid')
                        ->where('r.registration_uid', $errorEvent->registration_uid)
                        ->first();

                    if ($registration) {
                        // Trigger the event
                        event(new CreateParticipantInCyclingAppEvent(
                            $registration->course_uid,
                            $registration->person_uid,
                            $registration->registration_uid
                        ));

                        // Remove the error event since we're retrying
                        DB::table('error_events')
                            ->where('errorevent_uid', $errorEvent->errorevent_uid)
                            ->delete();

                        $retriedCount++;

                        // Add a small delay to prevent overwhelming the system
                        usleep(100000); // 0.1 second delay
                    } else {
                        $failedCount++;
                        $errors[] = "Registration not found for error event: {$errorEvent->errorevent_uid}";
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    $errors[] = "Failed to retry error event {$errorEvent->errorevent_uid}: " . $e->getMessage();
                    Log::error("Failed to retry error event {$errorEvent->errorevent_uid}: " . $e->getMessage());
                }
            }

            return response()->json([
                'success' => $failedCount === 0,
                'message' => "Retry completed. Successfully retried {$retriedCount} events, {$failedCount} failed",
                'retried_count' => $retriedCount,
                'failed_count' => $failedCount,
                'errors' => $errors,
                'timestamp' => now()->toISOString()
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Error retrying all publish events: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to retry all publish events',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}

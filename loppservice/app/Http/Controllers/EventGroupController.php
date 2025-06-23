<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Interfaces\EventGroupRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EventGroupController extends Controller
{
    private EventGroupRepositoryInterface $eventGroupRepository;

    public function __construct(EventGroupRepositoryInterface $eventGroupRepository)
    {
        $this->eventGroupRepository = $eventGroupRepository;
    }

    public function create(Request $request): JsonResponse
    {
        // Preprocess dates if they are in ISO format
        $this->preprocessDates($request);

        Log::debug("Handling create for event group: ");

        $validated = $request->validate([
            'uid' => 'required|string',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'startdate' => 'required|date_format:Y-m-d|after_or_equal:today',
            'enddate' => 'required|date_format:Y-m-d|after_or_equal:startdate',
            'event_uids' => 'array',
            'event_uids.*' => 'string|exists:events,event_uid'
        ]);

        try {
            DB::beginTransaction();

            $eventGroup = $this->eventGroupRepository->create($validated);

            if (!empty($validated['event_uids'])) {
                $this->eventGroupRepository->attachEvents($eventGroup, $validated['event_uids']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Event group created successfully',
                'data' => $eventGroup
            ], Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('Failed to create event group', [
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'validated' => $validated ?? null
            ]);

            return response()->json([
                'message' => 'Failed to create event group',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, string $uid): JsonResponse
    {
        // Preprocess dates if they are in ISO formatte
        $this->preprocessDates($request);

        Log::debug("Handling update for event group: " . $uid);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'startdate' => 'required|date_format:Y-m-d',
            'enddate' => 'required|date_format:Y-m-d|after_or_equal:startdate',
            'event_uids' => 'array',
            'event_uids.*' => 'string|exists:events,event_uid'
        ]);

        try {
            DB::beginTransaction();

            $eventGroup = $this->eventGroupRepository->update($uid, $validated);

            if (isset($validated['event_uids'])) {
                $this->eventGroupRepository->syncEvents($eventGroup, $validated['event_uids']);
            }

            DB::commit();

            return response()->json([
                'message' => 'Event group updated successfully',
                'data' => $eventGroup
            ]);
        } catch (\Exception $e) {
            DB::rollBack();

            // Log the error for debugging
            Log::error('Failed to update event group', [
                'uid' => $uid,
                'error' => $e->getMessage(),
                'request_data' => $request->all(),
                'validated' => $validated ?? null
            ]);

            return response()->json([
                'message' => 'Failed to update event group',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function delete(string $uid): JsonResponse
    {
        try {
            DB::beginTransaction();

            $eventGroup = $this->eventGroupRepository->findByUid($uid);

            if (!$eventGroup) {
                return response()->json([
                    'message' => 'Event group not found'
                ], Response::HTTP_NOT_FOUND);
            }


            $deleted = $this->eventGroupRepository->delete($uid);

            if (!$deleted) {
                throw new \Exception('Failed to delete event group');
            }

            DB::commit();

            return response()->json([
                'message' => 'Event group deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete event group: ' . $e->getMessage());
            return response()->json([
                'message' => 'Failed to delete event group',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function get(string $uid): JsonResponse
    {
        try {
            $eventGroup = $this->eventGroupRepository->findByUid($uid);

            if (!$eventGroup) {
                return response()->json([
                    'message' => 'Event group not found'
                ], Response::HTTP_NOT_FOUND);
            }

            return response()->json([
                'data' => $eventGroup
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get event group',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function all(): JsonResponse
    {
        try {
            $eventGroups = $this->eventGroupRepository->all();

            return response()->json([
                'data' => $eventGroups
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Failed to get event groups',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * Preprocess date fields to convert ISO format to Y-m-d format
     *
     * @param Request $request
     */
    private function preprocessDates(Request $request): void
    {
        $dateFields = ['startdate', 'enddate'];

        foreach ($dateFields as $field) {
            if ($request->has($field)) {
                $value = $request->input($field);

                // Check if it's an ISO date format (e.g., 2026-12-29T23:00:00.000Z)
                if (is_string($value) && preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $value)) {
                    try {
                        $date = new \DateTime($value);
                        $request->merge([$field => $date->format('Y-m-d')]);
                    } catch (\Exception $e) {
                        // If conversion fails, leave the original value for validation to catch it
                        Log::warning('Failed to convert ISO date', [
                            'field' => $field,
                            'value' => $value,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            }
        }
    }
}

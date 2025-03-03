<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Repositories\Interfaces\EventGroupRepositoryInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after_or_equal:startdate',
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
            return response()->json([
                'message' => 'Failed to create event group',
                'error' => $e->getMessage()
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'uid' => 'required|string|exists:event_groups,uid',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'startdate' => 'required|date',
            'enddate' => 'required|date|after_or_equal:startdate',
            'event_uids' => 'array',
            'event_uids.*' => 'string|exists:events,event_uid'
        ]);

        try {
            DB::beginTransaction();

            $eventGroup = $this->eventGroupRepository->update($validated['uid'], $validated);

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

            if ($this->eventGroupRepository->hasEventsWithRegistrationsOrOpen($eventGroup)) {
                return response()->json([
                    'message' => 'Cannot delete event group. Some events have registrations or are open for registration.',
                ], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $this->eventGroupRepository->delete($uid);

            DB::commit();

            return response()->json([
                'message' => 'Event group deleted successfully'
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
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
} 
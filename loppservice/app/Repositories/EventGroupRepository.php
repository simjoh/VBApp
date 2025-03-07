<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Event;
use App\Models\EventGroup;
use App\Repositories\Interfaces\EventGroupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventGroupRepository implements EventGroupRepositoryInterface
{
    public function create(array $data): EventGroup
    {
        if (!isset($data['uid'])) {
            throw new \Exception('Event group UID is required');
        }
        
        $eventGroup = new EventGroup();
        $eventGroup->fill($data);
        $eventGroup->save();

        return $eventGroup;
    }

    public function update(string $uid, array $data): EventGroup
    {
        $eventGroup = EventGroup::where('uid', $uid)->firstOrFail();
        $eventGroup->fill($data);
        $eventGroup->save();
        
        return $eventGroup->fresh();
    }

    public function delete(string $uid): bool
    {
        try {
            DB::beginTransaction();

            // Find the event group with its events
            $eventGroup = EventGroup::where('uid', $uid)->with('events')->firstOrFail();

            
            // Detach all events first
            foreach ($eventGroup->events as $event) {
                $event->event_group_uid = null;
                $event->save();
            }

            // Delete the event group
            $deleted = $eventGroup->delete();

            DB::commit();
            return $deleted;
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete event group: ' . $e->getMessage());
            throw $e;
        }
    }

    public function findByUid(string $uid): ?EventGroup
    {
        return EventGroup::where('uid', $uid)->with('events')->first();
    }

    public function all(): Collection
    {
        return EventGroup::with('events')->get();
    }

    public function hasEventsWithRegistrationsOrOpen(EventGroup $eventGroup): bool
    {
        return $eventGroup->events()
            ->where(function($query) {
                $query->has('registrations')
                      ->orWhere('registration_open', true);
            })->exists();
    }

    public function attachEvents(EventGroup $eventGroup, array $eventUids): void
    {
        Event::whereIn('event_uid', $eventUids)->update(['event_group_uid' => $eventGroup->uid]);
    }

    public function syncEvents(EventGroup $eventGroup, array $eventUids): void
    {
        // Clear previous events
        Event::where('event_group_uid', $eventGroup->uid)
            ->update(['event_group_uid' => null]);
            
        // Attach new events
        Event::whereIn('event_uid', $eventUids)
            ->update(['event_group_uid' => $eventGroup->uid]);
    }
} 
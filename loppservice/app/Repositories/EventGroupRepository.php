<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Event;
use App\Models\EventGroup;
use App\Repositories\Interfaces\EventGroupRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

class EventGroupRepository implements EventGroupRepositoryInterface
{
    public function create(array $data): EventGroup
    {
        
        $eventGroup = new EventGroup();
        $eventGroup->uid = (string) Str::uuid();
        $eventGroup->name = $data['name'];
        $eventGroup->description = $data['description'] ?? null;
        $eventGroup->startdate = $data['startdate'];
        $eventGroup->enddate = $data['enddate'];
        $eventGroup->save();

        return $eventGroup;
    }

    public function update(string $uid, array $data): EventGroup
    {
        $eventGroup = $this->findByUid($uid);
        
        if (!$eventGroup) {
            throw new \Exception('Event group not found');
        }

        $eventGroup->name = $data['name'];
        $eventGroup->description = $data['description'] ?? null;
        $eventGroup->startdate = $data['startdate'];
        $eventGroup->enddate = $data['enddate'];
        $eventGroup->save();

        return $eventGroup;
    }

    public function delete(string $uid): bool
    {
        $eventGroup = $this->findByUid($uid);
        
        if (!$eventGroup) {
            throw new \Exception('Event group not found');
        }

        return $eventGroup->delete();
    }

    public function findByUid(string $uid): ?EventGroup
    {
        return EventGroup::with('events')->where('uid', $uid)->first();
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
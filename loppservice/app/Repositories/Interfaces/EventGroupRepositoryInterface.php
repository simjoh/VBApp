<?php

declare(strict_types=1);

namespace App\Repositories\Interfaces;

use App\Models\EventGroup;
use Illuminate\Database\Eloquent\Collection;

interface EventGroupRepositoryInterface
{
    public function create(array $data): EventGroup;
    public function update(string $uid, array $data): EventGroup;
    public function delete(string $uid): bool;
    public function findByUid(string $uid): ?EventGroup;
    public function all(): Collection;
    public function hasEventsWithRegistrationsOrOpen(EventGroup $eventGroup): bool;
    public function attachEvents(EventGroup $eventGroup, array $eventUids): void;
    public function syncEvents(EventGroup $eventGroup, array $eventUids): void;
} 
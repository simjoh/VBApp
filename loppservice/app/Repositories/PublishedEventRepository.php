<?php

namespace App\Repositories;

use App\Models\PublishedEvents;
use App\Repositories\Interfaces\PublishedEventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * Class PublishedEventRepository
 *
 * This repository handles all database operations for the PublishedEvents model.
 *
 * Usage Examples:
 * ```php
 * // In your controller or service:
 * use App\Repositories\Interfaces\PublishedEventRepositoryInterface;
 *
 * class PublishedEventService
 * {
 *     protected $publishedEventRepository;
 *
 *     public function __construct(PublishedEventRepositoryInterface $publishedEventRepository)
 *     {
 *         $this->publishedEventRepository = $publishedEventRepository;
 *     }
 *
 *     public function createPublishedEvent(array $data)
 *     {
 *         // Using new and save pattern
 *         $publishedEvent = $this->publishedEventRepository->new([
 *             'publishedevent_uid' => Uuid::uuid4(),
 *             'registration_uid' => $data['registration_uid'],
 *             'type' => 'eventregistration'
 *         ]);
 *
 *         // Save when ready
 *         $this->publishedEventRepository->save($publishedEvent);
 *
 *         return $publishedEvent;
 *     }
 *
 *     public function findByRegistrationId(string $registrationUid)
 *     {
 *         return $this->publishedEventRepository->findByRegistrationUid($registrationUid);
 *     }
 * }
 * ```
 */
class PublishedEventRepository extends BaseRepository implements PublishedEventRepositoryInterface
{
    /**
     * @param PublishedEvents $model
     */
    public function __construct(PublishedEvents $model)
    {
        parent::__construct($model);
    }

    /**
     * Find published event by registration UID
     *
     * @param string $registrationUid
     * @return Model|null
     */
    public function findByRegistrationUid(string $registrationUid): ?Model
    {
        return $this->model->where('registration_uid', $registrationUid)->first();
    }

    /**
     * Find published events by type
     *
     * @param string $type
     * @return Collection
     */
    public function findByType(string $type): Collection
    {
        return $this->model->where('type', $type)->get();
    }

    /**
     * Find published events within a date range
     *
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return Collection
     */
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('created_at', [$startDate, $endDate])->get();
    }

    /**
     * Find the latest published event
     *
     * @return Model|null
     */
    public function findLatest(): ?Model
    {
        return $this->model->orderBy('created_at', 'desc')->first();
    }
}

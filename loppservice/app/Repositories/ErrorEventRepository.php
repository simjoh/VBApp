<?php

namespace App\Repositories;

use App\Models\ErrorEvents;
use App\Repositories\Interfaces\ErrorEventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Ramsey\Uuid\Uuid;

/**
 * Class ErrorEventRepository
 *
 * This repository handles all database operations for the ErrorEvents model.
 *
 * Usage Examples:
 * ```php
 * // In your controller or service:
 * use App\Repositories\Interfaces\ErrorEventRepositoryInterface;
 *
 * class ErrorEventService
 * {
 *     protected $errorEventRepository;
 *
 *     public function __construct(ErrorEventRepositoryInterface $errorEventRepository)
 *     {
 *         $this->errorEventRepository = $errorEventRepository;
 *     }
 *
 *     public function logError(array $data)
 *     {
 *         // Using new and save pattern
 *         $errorEvent = $this->errorEventRepository->new([
 *             'errorevent_uid' => Uuid::uuid4(),
 *             'publishedevent_uid' => $data['publishedevent_uid'],
 *             'registration_uid' => $data['registration_uid'],
 *             'type' => 'eventregistration'
 *         ]);
 *
 *         // Save when ready
 *         $this->errorEventRepository->save($errorEvent);
 *
 *         return $errorEvent;
 *     }
 *
 *     public function findByRegistrationId(string $registrationUid)
 *     {
 *         return $this->errorEventRepository->findByRegistrationUid($registrationUid);
 *     }
 * }
 * ```
 */
class ErrorEventRepository extends BaseRepository implements ErrorEventRepositoryInterface
{
    /**
     * @param ErrorEvents $model
     */
    public function __construct(ErrorEvents $model)
    {
        parent::__construct($model);
    }

    /**
     * Find error event by registration UID
     *
     * @param string $registrationUid
     * @return Model|null
     */
    public function findByRegistrationUid(string $registrationUid): ?Model
    {
        return $this->model->where('registration_uid', $registrationUid)->first();
    }

    /**
     * Find error events by published event UID
     *
     * @param string $publishedEventUid
     * @return Model|null
     */
    public function findByPublishedEventUid(string $publishedEventUid): ?Model
    {
        return $this->model->where('publishedevent_uid', $publishedEventUid)->first();
    }

    /**
     * Find error events by type
     *
     * @param string $type
     * @return Collection
     */
    public function findByType(string $type): Collection
    {
        return $this->model->where('type', $type)->get();
    }

    /**
     * Find error events within a date range
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
     * Find the latest error event
     *
     * @return Model|null
     */
    public function findLatest(): ?Model
    {
        return $this->model->orderBy('created_at', 'desc')->first();
    }
}

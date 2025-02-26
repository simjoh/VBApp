<?php

namespace App\Repositories;

use App\Models\Event;
use App\Repositories\Interfaces\EventRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class EventRepository
 *
 * This repository handles all database operations for the Event model.
 *
 * Usage Examples:
 * ```php
 * // In your controller or service:
 * use App\Repositories\Interfaces\EventRepositoryInterface;
 *
 * class EventService
 * {
 *     protected $eventRepository;
 *
 *     public function __construct(EventRepositoryInterface $eventRepository)
 *     {
 *         $this->eventRepository = $eventRepository;
 *     }
 *
 *     public function createEvent(array $data)
 *     {
 *         return $this->eventRepository->create([
 *             'title' => $data['title'],
 *             'description' => $data['description'],
 *             'startdate' => $data['start_date'],
 *             'enddate' => $data['end_date'],
 *             'completed' => false,
 *             'event_type' => $data['event_type'] ?? null,
 *             'organizer_id' => $data['organizer_id'] ?? null
 *         ]);
 *     }
 *
 *     public function searchEvents(array $filters)
 *     {
 *         // Using findBy with criteria
 *         return $this->eventRepository->findBy([
 *             'event_type' => $filters['type'],
 *             'completed' => false
 *         ], ['startdate' => 'asc'], 10);
 *
 *         // Or using specific methods
 *         if ($filters['type'] === 'MSR') {
 *             return $this->eventRepository->findByEventType('MSR');
 *         }
 *     }
 * }
 * ```
 */
class EventRepository extends BaseRepository implements EventRepositoryInterface
{
    /**
     * @param Event $model
     */
    public function __construct(Event $model)
    {
        parent::__construct($model);
    }

    /**
     * Find an event by its UUID
     *
     * @param string $eventUid
     * @return Model|null
     */
    public function findByUid(string $eventUid): ?Model
    {
        return $this->model->where('event_uid', $eventUid)->first();
    }

    /**
     * Search events by title (partial match)
     *
     * @param string $title
     * @return Collection
     */
    public function findByTitle(string $title): Collection
    {
        return $this->model->where('title', 'like', "%{$title}%")->get();
    }

    /**
     * Find events within a date range
     *
     * @param string $startDate Format: Y-m-d
     * @param string $endDate Format: Y-m-d
     * @return Collection
     */
    public function findByDateRange(string $startDate, string $endDate): Collection
    {
        return $this->model->whereBetween('startdate', [$startDate, $endDate])
            ->orWhereBetween('enddate', [$startDate, $endDate])
            ->get();
    }

    /**
     * Get all non-completed events
     *
     * @return Collection
     */
    public function findActive(): Collection
    {
        return $this->model->where('completed', false)->get();
    }

    /**
     * Find events by type (e.g., 'MSR', 'BRM')
     *
     * @param string $eventType
     * @return Collection
     */
    public function findByEventType(string $eventType): Collection
    {
        return $this->model->where('event_type', $eventType)->get();
    }

    /**
     * Find events by organizer
     *
     * @param int $organizerId
     * @return Collection
     */
    public function findByOrganizer(int $organizerId): Collection
    {
        return $this->model->where('organizer_id', $organizerId)->get();
    }

    /**
     * Find events that use Stripe payment
     *
     * @return Collection
     */
    public function findWithStripePayment(): Collection
    {
        return $this->model->whereHas('eventconfiguration', function($query) {
            $query->where('use_stripe_payment', true);
        })->get();
    }
}

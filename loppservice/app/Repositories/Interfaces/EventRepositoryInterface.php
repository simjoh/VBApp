<?php

namespace App\Repositories\Interfaces;

/**
 * Interface EventRepositoryInterface
 *
 * Usage:
 * ```php
 * class EventController extends Controller
 * {
 *     protected $eventRepository;
 *
 *     public function __construct(EventRepositoryInterface $eventRepository)
 *     {
 *         $this->eventRepository = $eventRepository;
 *     }
 *
 *     public function index()
 *     {
 *         // Get all events
 *         $allEvents = $this->eventRepository->all();
 *
 *         // Find active events
 *         $activeEvents = $this->eventRepository->findActive();
 *
 *         // Search by date range
 *         $events = $this->eventRepository->findByDateRange('2024-01-01', '2024-12-31');
 *
 *         // Find events using Stripe
 *         $stripeEvents = $this->eventRepository->findWithStripePayment();
 *     }
 * }
 * ```
 */
interface EventRepositoryInterface extends BaseRepositoryInterface
{
    public function findByUid(string $eventUid);
    public function findByTitle(string $title);
    public function findByDateRange(string $startDate, string $endDate);
    public function findActive();
    public function findByEventType(string $eventType);
    public function findByOrganizer(int $organizerId);
    public function findWithStripePayment();
}

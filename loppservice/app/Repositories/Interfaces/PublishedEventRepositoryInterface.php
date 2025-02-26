<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface PublishedEventRepositoryInterface
 *
 * Usage:
 * ```php
 * class PublishedEventController extends Controller
 * {
 *     protected $publishedEventRepository;
 *
 *     public function __construct(PublishedEventRepositoryInterface $publishedEventRepository)
 *     {
 *         $this->publishedEventRepository = $publishedEventRepository;
 *     }
 *
 *     public function index()
 *     {
 *         // Get all published events
 *         $events = $this->publishedEventRepository->all();
 *
 *         // Find by registration UID
 *         $event = $this->publishedEventRepository->findByRegistrationUid($registrationUid);
 *
 *         // Find by type
 *         $events = $this->publishedEventRepository->findByType('eventregistration');
 *     }
 * }
 * ```
 */
interface PublishedEventRepositoryInterface extends BaseRepositoryInterface
{
    public function findByRegistrationUid(string $registrationUid): ?Model;
    public function findByType(string $type): Collection;
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function findLatest(): ?Model;
}

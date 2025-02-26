<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface ErrorEventRepositoryInterface
 *
 * Usage:
 * ```php
 * class ErrorEventController extends Controller
 * {
 *     protected $errorEventRepository;
 *
 *     public function __construct(ErrorEventRepositoryInterface $errorEventRepository)
 *     {
 *         $this->errorEventRepository = $errorEventRepository;
 *     }
 *
 *     public function index()
 *     {
 *         // Get all error events
 *         $errors = $this->errorEventRepository->all();
 *
 *         // Find by registration UID
 *         $error = $this->errorEventRepository->findByRegistrationUid($registrationUid);
 *
 *         // Find by published event UID
 *         $error = $this->errorEventRepository->findByPublishedEventUid($publishedEventUid);
 *
 *         // Find by type
 *         $errors = $this->errorEventRepository->findByType('eventregistration');
 *     }
 * }
 * ```
 */
interface ErrorEventRepositoryInterface extends BaseRepositoryInterface
{
    public function findByRegistrationUid(string $registrationUid): ?Model;
    public function findByPublishedEventUid(string $publishedEventUid): ?Model;
    public function findByType(string $type): Collection;
    public function findByDateRange(string $startDate, string $endDate): Collection;
    public function findLatest(): ?Model;
}

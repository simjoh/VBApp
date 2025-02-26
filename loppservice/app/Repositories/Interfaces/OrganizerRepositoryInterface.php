<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface OrganizerRepositoryInterface
 *
 * Usage:
 * ```php
 * class OrganizerController extends Controller
 * {
 *     protected $organizerRepository;
 *
 *     public function __construct(OrganizerRepositoryInterface $organizerRepository)
 *     {
 *         $this->organizerRepository = $organizerRepository;
 *     }
 *
 *     public function index()
 *     {
 *         // Get all active organizers
 *         $activeOrganizers = $this->organizerRepository->findActive();
 *
 *         // Find organizers by name
 *         $organizers = $this->organizerRepository->findByName('Cykelintresset');
 *
 *         // Find organizers with GDPR consent
 *         $consentedOrganizers = $this->organizerRepository->findWithGdprConsent();
 *     }
 * }
 * ```
 */
interface OrganizerRepositoryInterface extends BaseRepositoryInterface
{
    public function findByName(string $name);
    public function findByEmail(string $email);
    public function findActive();
    public function findWithGdprConsent();
    public function findByOrganizationNumber(string $organizationNumber);
}

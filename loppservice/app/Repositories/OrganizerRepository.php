<?php

namespace App\Repositories;

use App\Models\Organizer;
use App\Repositories\Interfaces\OrganizerRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class OrganizerRepository
 *
 * This repository handles all database operations for the Organizer model.
 *
 * Usage Examples:
 * ```php
 * // In your controller or service:
 * use App\Repositories\Interfaces\OrganizerRepositoryInterface;
 *
 * class OrganizerService
 * {
 *     protected $organizerRepository;
 *
 *     public function __construct(OrganizerRepositoryInterface $organizerRepository)
 *     {
 *         $this->organizerRepository = $organizerRepository;
 *     }
 *
 *     public function createOrganizer(array $data)
 *     {
 *         return $this->organizerRepository->create([
 *             'organization_name' => $data['name'],
 *             'organization_number' => $data['org_number'],
 *             'contact_person_name' => $data['contact_name'],
 *             'email' => $data['email'],
 *             'phone' => $data['phone'],
 *             'active' => true
 *         ]);
 *     }
 *
 *     public function findActiveOrganizers()
 *     {
 *         return $this->organizerRepository->findActive();
 *     }
 * }
 * ```
 */
class OrganizerRepository extends BaseRepository implements OrganizerRepositoryInterface
{
    /**
     * @param Organizer $model
     */
    public function __construct(Organizer $model)
    {
        parent::__construct($model);
    }

    /**
     * Find organizers by name (partial match)
     *
     * @param string $name
     * @return Collection
     */
    public function findByName(string $name): Collection
    {
        return $this->model->where('organization_name', 'like', "%{$name}%")->get();
    }

    /**
     * Find an organizer by email address
     *
     * @param string $email
     * @return Model|null
     */
    public function findByEmail(string $email): ?Model
    {
        return $this->model->where('email', $email)->first();
    }

    /**
     * Get all active organizers
     *
     * @return Collection
     */
    public function findActive(): Collection
    {
        return $this->model->where('active', true)->get();
    }

    /**
     * Find organizers who have given GDPR consent
     *
     * @return Collection
     */
    public function findWithGdprConsent(): Collection
    {
        return $this->model->where('gdpr_consent', true)->get();
    }

    /**
     * Find an organizer by organization number
     *
     * @param string $organizationNumber
     * @return Model|null
     */
    public function findByOrganizationNumber(string $organizationNumber): ?Model
    {
        return $this->model->where('organization_number', $organizationNumber)->first();
    }
}

<?php

namespace App\Repositories;

use App\Models\Club;
use App\Repositories\Interfaces\ClubRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Class ClubRepository
 *
 * This repository handles all database operations for the Club model.
 *
 * Usage Examples:
 * ```php
 * // In your controller or service:
 * use App\Repositories\Interfaces\ClubRepositoryInterface;
 *
 * class ClubService
 * {
 *     protected $clubRepository;
 *
 *     public function __construct(ClubRepositoryInterface $clubRepository)
 *     {
 *         $this->clubRepository = $clubRepository;
 *     }
 *
 *     public function createClub(array $data)
 *     {
 *         return $this->clubRepository->create([
 *             'name' => $data['name'],
 *             'description' => $data['description'] ?? null,
 *             'official_club' => $data['official_club'] ?? false
 *         ]);
 *     }
 *
 *     public function searchClubs(string $name)
 *     {
 *         return $this->clubRepository->findByName($name);
 *     }
 * }
 * ```
 */
class ClubRepository extends BaseRepository implements ClubRepositoryInterface
{
    /**
     * @param Club $model
     */
    public function __construct(Club $model)
    {
        parent::__construct($model);
    }

    /**
     * Find clubs by name (partial match)
     *
     * @param string $name
     * @return Collection
     */
    public function findByName(string $name): Collection
    {
        return $this->model->where('name', 'like', "%{$name}%")->get();
    }

    /**
     * Find a club by its UUID
     *
     * @param string $uuid
     * @return Model|null
     */
    public function findByUuid(string $uuid): ?Model
    {
        return $this->model->where('club_uid', $uuid)->first();
    }

    /**
     * Get all official clubs
     *
     * @return Collection
     */
    public function findOfficialClubs(): Collection
    {
        return $this->model->where('official_club', true)->get();
    }

    /**
     * Find a club by exact name match
     *
     * @param string $name
     * @return Model|null
     */
    public function findByNameExact(string $name): ?Model
    {
        return $this->model->where('name', $name)->first();
    }

    /**
     * Delete a club by its UUID
     *
     * @param string $uuid
     * @return bool
     */
    public function deleteByUuid(string $uuid): bool
    {
        $club = $this->findByUuid($uuid);

        if (!$club) {
            return false;
        }

        return $club->delete();
    }
}

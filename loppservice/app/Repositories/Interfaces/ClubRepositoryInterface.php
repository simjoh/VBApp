<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface ClubRepositoryInterface
 *
 * Usage:
 * ```php
 * class ClubController extends Controller
 * {
 *     protected $clubRepository;
 *
 *     public function __construct(ClubRepositoryInterface $clubRepository)
 *     {
 *         $this->clubRepository = $clubRepository;
 *     }
 *
 *     public function index()
 *     {
 *         // Get all official clubs
 *         $officialClubs = $this->clubRepository->findOfficialClubs();
 *
 *         // Find clubs by name
 *         $clubs = $this->clubRepository->findByName('Cykelintresset');
 *
 *         // Find club by UUID
 *         $club = $this->clubRepository->findByUuid('88d01283-e18e-44d5-89f7-ced4b91d01f9');
 *     }
 * }
 * ```
 */
interface ClubRepositoryInterface extends BaseRepositoryInterface
{
    public function findByName(string $name);
    public function findByUuid(string $uuid);
    public function findOfficialClubs();
    public function findByNameExact(string $name);
}

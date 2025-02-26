<?php

namespace App\Repositories\Interfaces;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Interface BaseRepositoryInterface
 *
 * Defines the standard operations to be implemented by all repositories
 */
interface BaseRepositoryInterface
{
    public function all(): Collection;
    public function find(int $id): ?Model;
    public function create(array $data): Model;
    public function new(array $attributes): Model;
    public function save(Model $model): bool;
    public function update(int $id, array $data): bool;
    public function delete(int $id): bool;
    public function findBy(array $criteria, array $orderBy = null, $limit = null): Collection;
}

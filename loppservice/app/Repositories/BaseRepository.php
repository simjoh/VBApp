<?php

namespace App\Repositories;

use App\Repositories\Interfaces\BaseRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * Abstract BaseRepository class that implements common repository methods
 *
 * Usage:
 * ```php
 * class YourRepository extends BaseRepository
 * {
 *     public function __construct(YourModel $model)
 *     {
 *         parent::__construct($model);
 *     }
 *
 *     // Add your specific repository methods here
 * }
 *
 * // Using the new and save pattern:
 * $repository = new YourRepository(new YourModel());
 * $entity = $repository->new([
 *     'name' => 'Test Name',
 *     'description' => 'Test Description'
 * ]);
 * // Modify entity if needed
 * $entity->additional_field = 'value';
 * // Save when ready
 * $repository->save($entity);
 * ```
 */
abstract class BaseRepository implements BaseRepositoryInterface
{
    /**
     * @var Model
     */
    protected $model;

    /**
     * @param Model $model
     */
    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    /**
     * Get all records
     *
     * @return Collection
     */
    public function all(): Collection
    {
        return $this->model->all();
    }

    /**
     * Find a record by its ID
     *
     * @param int $id
     * @return Model|null
     */
    public function find(int $id): ?Model
    {
        return $this->model->find($id);
    }

    /**
     * Create a new record
     *
     * @param array $data
     * @return Model
     */
    public function create(array $data): Model
    {
        return $this->model->create($data);
    }

    /**
     * Create a new model instance without saving to database
     *
     * @param array $attributes
     * @return Model
     */
    public function new(array $attributes = []): Model
    {
        return $this->model->newInstance($attributes);
    }

    /**
     * Save a model instance to database
     *
     * @param Model $model
     * @return bool
     */
    public function save(Model $model): bool
    {
        return $model->save();
    }

    /**
     * Update an existing record
     *
     * @param int $id
     * @param array $data
     * @return bool
     */
    public function update(int $id, array $data): bool
    {
        return $this->model->find($id)->update($data);
    }

    /**
     * Delete a record
     *
     * @param int $id
     * @return bool
     */
    public function delete(int $id): bool
    {
        return $this->model->find($id)->delete();
    }

    /**
     * Find records by multiple criteria
     *
     * Example:
     * ```php
     * $records = $repository->findBy(
     *     ['status' => 'active', 'type' => 'user'],
     *     ['created_at' => 'desc'],
     *     10
     * );
     * ```
     *
     * @param array $criteria
     * @param array|null $orderBy
     * @param int|null $limit
     * @return Collection
     */
    public function findBy(array $criteria, array $orderBy = null, $limit = null): Collection
    {
        $query = $this->model->query();

        foreach ($criteria as $key => $value) {
            $query->where($key, $value);
        }

        if ($orderBy) {
            foreach ($orderBy as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        if ($limit) {
            $query->limit($limit);
        }

        return $query->get();
    }

    /**
     * Get the model instance
     *
     * @return Model
     */
    protected function getModel(): Model
    {
        return $this->model;
    }
}

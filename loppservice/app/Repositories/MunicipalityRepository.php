<?php

namespace App\Repositories;

use App\Models\Municipality;
use App\Repositories\Interfaces\MunicipalityRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class MunicipalityRepository extends BaseRepository implements MunicipalityRepositoryInterface
{
    public function __construct(Municipality $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $municipalityCode): ?Model
    {
        return $this->model->where('municipality_code', $municipalityCode)->first();
    }

    public function findByCountyId(int $countyId): Collection
    {
        return $this->model->where('county_id', $countyId)->get();
    }

    public function findByCountyCode(string $countyCode): Collection
    {
        return $this->model->whereHas('county', function($query) use ($countyCode) {
            $query->where('county_code', $countyCode);
        })->get();
    }
}

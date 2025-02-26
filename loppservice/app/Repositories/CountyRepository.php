<?php

namespace App\Repositories;

use App\Models\County;
use App\Repositories\Interfaces\CountyRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

class CountyRepository extends BaseRepository implements CountyRepositoryInterface
{
    public function __construct(County $model)
    {
        parent::__construct($model);
    }

    public function findByCode(string $countyCode): ?Model
    {
        return $this->model->where('county_code', $countyCode)->first();
    }

    public function getMunicipalitiesByCountyCode(string $countyCode): Collection
    {
        return $this->model->where('county_code', $countyCode)
            ->with('municipalities')
            ->first()
            ->municipalities;
    }
}

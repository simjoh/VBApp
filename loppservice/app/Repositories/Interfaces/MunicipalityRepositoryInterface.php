<?php

namespace App\Repositories\Interfaces;

interface MunicipalityRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCode(string $municipalityCode);
    public function findByCountyId(int $countyId);
    public function findByCountyCode(string $countyCode);
}

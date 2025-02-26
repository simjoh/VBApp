<?php

namespace App\Repositories\Interfaces;

interface CountyRepositoryInterface extends BaseRepositoryInterface
{
    public function findByCode(string $countyCode);
    public function getMunicipalitiesByCountyCode(string $countyCode);
}

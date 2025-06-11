<?php

namespace App\Domain\Model\Country\Service;

use App\common\Service\ServiceAbstract;
use App\Domain\Model\Country\Country;
use App\Domain\Model\Country\Repository\CountryRepository;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class CountryService extends ServiceAbstract
{

    private $countryrepository;
    private $permissionrepository;

    public function __construct(ContainerInterface $c, CountryRepository $countryRepository, PermissionRepository $permissionRepository)
    {
        $this->countryrepository = $countryRepository;
        $this->permissionrepository = $permissionRepository;

    }

    /**
     * Get all countries
     *
     * @param string $currentuser_id
     * @return array
     */
    public function getAllCountries(string $currentuser_id): array
    {
        $permissions = $this->getPermissions($currentuser_id);
        return $this->countryrepository->allCountries();
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("COUNTRY", $user_uid);
    }

    public function getCountryById(int $countryId): ?Country
    {
        return $this->countryrepository->countryFor($countryId);
    }
}
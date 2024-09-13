<?php

namespace App\Domain\Model\Country\Service;

use App\common\Service\ServiceAbstract;
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

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("COUNTRY", $user_uid);
    }
}
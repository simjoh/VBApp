<?php

namespace App\Domain\Model\Club\Service;

use App\Domain\Model\Club\ClubRepository;
use App\Domain\Model\Club\Rest\ClubAssembly;
use App\Domain\Model\Club\Rest\ClubRepresentation;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class ClubService
{

    public function __construct(ContainerInterface $c,
                                ClubRepository     $clubRepository, PermissionRepository $permissionRepository, ClubAssembly $clubAssembly)
    {

        $this->settings = $c->get('settings');
        $this->clubrepository = $clubRepository;
        $this->permissionrepoitory = $permissionRepository;
        $this->clubAssembly = $clubAssembly;

    }

    public function getClubByUid(string $club_uid, string $currentuser_id): ?ClubRepresentation
    {
        $permissions = $this->getPermissions($currentuser_id);
        $club = $this->clubrepository->getClubByUId($club_uid);
        return $this->clubAssembly->toRepresentation($club,$permissions);
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepoitory->getPermissionsTodata("CLUB",$user_uid);
    }

}
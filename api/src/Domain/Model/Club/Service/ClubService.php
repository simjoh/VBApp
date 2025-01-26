<?php

namespace App\Domain\Model\Club\Service;

use App\common\CurrentUser;
use App\common\Rest\Link;
use App\Domain\Model\Club\ClubRepository;
use App\Domain\Model\Club\Rest\ClubAssembly;
use App\Domain\Model\Club\Rest\ClubRepresentation;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class ClubService
{

    private $clubrepository;
    private $permissionrepoitory;
    private $clubAssembly;
    private $settings;

    public function __construct(ContainerInterface   $c,
                                ClubRepository       $clubRepository,
                                PermissionRepository $permissionRepository,
                                ClubAssembly         $clubAssembly)
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
        if ($club != null) {
            return $this->clubAssembly->toRepresentation($club, []);
        }
        return new ClubRepresentation();
    }

    public function getAllClubs(): ?array
    {
        $permissions = $this->getPermissions(CurrentUser::getUser()->getId());
        $clubs = $this->clubrepository->getAllClubs();
        return $this->clubAssembly->toRepresentations($clubs,CurrentUser::getUser()->getId() );
    }


    public function createClub(string $currentuser_id, ClubRepresentation $clubRepresentation): ?ClubRepresentation
    {
        $permissions = $this->getPermissions($currentuser_id);
        $club = $this->clubrepository->getClubByTitleLower($clubRepresentation->getTitle());
        if ($club == null) {
            $club_uid = $this->clubrepository->createClub($clubRepresentation->getAcpCode(), $clubRepresentation->getTitle());
        }
        $clubreturn = $this->clubrepository->getClubByUId($club_uid);
        return $this->clubAssembly->toRepresentation($clubreturn, $permissions);
    }

    public function updateClub(string $currentuser_id, ClubRepresentation $clubRepresentation): ?ClubRepresentation
    {
        $club = $this->clubrepository->getClubByUId($clubRepresentation->getClubUid());
        $permissions = $this->getPermissions($currentuser_id);
        $clubReturn = $this->clubrepository->updateClub($club);
        return $this->clubAssembly->toRepresentation($clubReturn, $permissions);
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepoitory->getPermissionsTodata("CLUB", $user_uid);
    }

}
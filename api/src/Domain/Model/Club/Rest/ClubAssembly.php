<?php

namespace App\Domain\Model\Club\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Club\Club;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class ClubAssembly
{



    public function __construct(PermissionRepository $permissionRepository, ContainerInterface $c)
    {
        $this->permissinrepository = $permissionRepository;
        $this->settings = $c->get('settings');
    }


    public function toRepresentations(array $clubsArray, string $currentUserUid): array {
        $permissions = $this->getPermissions($currentUserUid);
        $clubs = array();
        foreach ($clubsArray as $x =>  $club) {
            array_push($clubs, (object) $this->toRepresentation($club,$permissions));
        }
        return $clubs;
    }


    public function toRepresentation( $club,  array $permissions): ?ClubRepresentation {
        $clubrepr = new ClubRepresentation();

        $clubrepr->setClubUid($club->getClubUid());
        $clubrepr->setTitle($club->getTitle());
        $clubrepr->setAcpCode($club->getAcpKod());

        $linkArray = array();

        array_push($linkArray, new Link("self", 'GET', $this->settings['path'] . 'club/' . $club->getClubUid()));
        $clubrepr->setLinks($linkArray);

        return $clubrepr;

    }


    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("EVENT",$user_uid);

    }





}
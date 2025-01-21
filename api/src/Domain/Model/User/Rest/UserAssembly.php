<?php

namespace App\Domain\Model\User\Rest;

use App\common\Rest\Link;
use App\Domain\Model\User\Repository\UserInfoRepository;
use App\Domain\Model\User\User;
use App\Domain\Permission\PermissionRepository;

class UserAssembly
{

    private $permissinrepository;
    private $userinforepository;
    private $userInfoAssembly;

    public function __construct(PermissionRepository $permissionRepository,
                                UserInfoRepository $userInfoRepository, UserInfoAssembly $userInfoAssembly)
    {
        $this->permissinrepository = $permissionRepository;
        $this->userinforepository = $userInfoRepository;
        $this->userInfoAssembly = $userInfoAssembly;

    }

    public function toRepresentations(array $UserArray, string $currentUserUid): array {
        $userArray = array();

        $permissions = $this->getPermissions($currentUserUid);

        foreach ($UserArray as $x =>  $site) {
            array_push($userArray, (object) $this->toRepresentation($site, $permissions));
        }
        return $userArray;
    }
    public function toRepresentation(User $s, array $permissions): UserRepresentation {
        $userRepresentation = new UserRepresentation();
        $userRepresentation->setUserUid($s->getId());
        $userRepresentation->setUsername($s->getUsername());
        $userRepresentation->setGivenname($s->getGivenname());
        $userRepresentation->setFamilyname($s->getFamilyname());
        $userRepresentation->setOrganizerId($s->getOrganizerId());
        $userRepresentation->setRoles($s->getRoles());

        $userinfo = $this->userinforepository->userinfoFor($s->getId());
        if(isset($userinfo)){
            $userRepresentation->setUserInfoRepresentation($this->userInfoAssembly->toRepresentation($userinfo));
        }

        // bygg på med lite länkar

        $linkArray = array();
        foreach ($permissions as $x =>  $site) {
            if($site->hasWritePermission()){
                array_push($linkArray, new Link("relation.user.update", 'PUT', '/api/user/' . $s->getId()));
                array_push($linkArray, new Link("relation.user.delete", 'DELETE', '/api/user/' . $s->getId()));
                array_push($linkArray, new Link("self", 'GET', '/api/user/' . $s->getId()));
                break;
            }
            if($site->hasReadPermission()){
                array_push($linkArray, new Link("self", 'GET', '/api/user/' . $s->getId()));
                array_push($linkArray, new Link("relation.organizers.organizer", 'GET', '/api/organizers/organizer/' . $s->getOrganizerId()));
            };
        }
        $userRepresentation->setLinks($linkArray);
        return $userRepresentation;
    }

    public function toUser(UserRepresentation $site): User{
        $user = new User();
        $user->setGivenname($site->getGivenname());
        $user->setFamilyname($site->getFamilyname());
        $user->setUsername($site->getUsername());
        return $user;
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("USER",$user_uid);
    }

}
<?php

namespace App\Domain\Model\User\Service;

use App\common\Rest\Link;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\User\Rest\UserRepresentation;
use App\Domain\Model\User\User;
use App\Domain\Permission\PermissionRepository;

class UserService extends  ServiceAbstract
{

    /**
     * @var UserRepository
     */
    private UserRepository $repository;

    public function __construct(UserRepository $repository, PermissionRepository $permissionRepository)
    {
        $this->repository = $repository;
        $this->permissionrepository = $permissionRepository;
    }
    public function getAllUsers(string $currentUseruid): ?array {
        $allUsers = $this->repository->getAllUSers();

        if (isset($allUsers)) {
            return $this->toRepresentations($allUsers, $currentUseruid);
        }
        return null;
    }

    public function getUserById($id, string $currentuserUid): ?UserRepresentation {
        $allUsers = $this->repository->getUserById($id);

        $permissions = $this->getPermissions($currentuserUid);
        if (isset($allUsers)) {
            return $this->toRepresentation($allUsers,$permissions);
        }
        return null;
    }

    public function updateUser($id, User $userParsed, string $currentUserUIDInSystem): ?UserRepresentation {
        $user = $this->repository->updateUser($id, $userParsed);
        if (isset($user)) {
            return $this->toRepresentation($user,$this->getPermissions($currentUserUIDInSystem));
        }

        return null;
    }

    public function createUser(UserRepresentation $userrepresentation, string $currentuser): UserRepresentation {
       $newUser = $this->repository->createUser($this->toSite($userrepresentation));
       return $this->toRepresentation($newUser,$this->getPermissions($currentuser));
    }

    public function deleteUser($user_uid): void{
        $this->repository->deleteUser($user_uid);
    }


    private function toRepresentations(array $UserArray, string $currentUserUid): array {
        $userArray = array();

        $permissions = $this->getPermissions($currentUserUid);

        foreach ($UserArray as $x =>  $site) {
            array_push($userArray, (object) $this->toRepresentation($site, $permissions));
        }
        return $userArray;
    }
    private function toRepresentation(User $s, array $permissions): UserRepresentation {
        $userRepresentation = new UserRepresentation();
        $userRepresentation->setUserUid($s->getId());
        $userRepresentation->setUsername($s->getUsername());
        $userRepresentation->setGivenname($s->getGivenname());
        $userRepresentation->setFamilyname($s->getFamilyname());
        $userRepresentation->setRoles($s->getRoles());
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
            };
        }
        $userRepresentation->setLinks($linkArray);
        return $userRepresentation;
    }

    private function toSite(UserRepresentation $site){
        $user = new User();
        $user->setGivenname($site->getGivenname());
        $user->setFamilyname($site->getFamilyname());
        $user->setUsername($site->getUsername());
        return $user;
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("USER",$user_uid);
    }
}
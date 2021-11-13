<?php

namespace App\Domain\Model\User\Service;

use App\common\Service\ServiceAbstract;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\User\Rest\UserAssembly;
use App\Domain\Model\User\Rest\UserRepresentation;
use App\Domain\Model\User\User;
use App\Domain\Permission\PermissionRepository;

class UserService extends  ServiceAbstract
{

    /**
     * @var UserRepository
     */
    private UserRepository $repository;

    public function __construct(UserRepository $repository, PermissionRepository $permissionRepository, UserAssembly $userAssembly)
    {
        $this->repository = $repository;
        $this->permissionrepository = $permissionRepository;
        $this->userAssembly = $userAssembly;
    }
    public function getAllUsers(string $currentUseruid): ?array {
        $allUsers = $this->repository->getAllUSers();

        if (isset($allUsers)) {
            return $this->userAssembly->toRepresentations($allUsers, $currentUseruid);
        }
        return null;
    }

    public function getUserById($id, string $currentuserUid): ?UserRepresentation {
        $allUsers = $this->repository->getUserById($id);

        $permissions = $this->getPermissions($currentuserUid);
        if (isset($allUsers)) {
            return $this->userAssembly->toRepresentation($allUsers,$permissions);
        }
        return null;
    }

    public function updateUser($id, User $userParsed, string $currentUserUIDInSystem): ?UserRepresentation {
        $user = $this->repository->updateUser($id, $userParsed);
        if (isset($user)) {
            return $this->userAssembly->toRepresentation($user,$this->getPermissions($currentUserUIDInSystem));
        }

        return null;
    }

    public function createUser(UserRepresentation $userrepresentation, string $currentuser): UserRepresentation {
       $newUser = $this->repository->createUser($this->userAssembly->toUser($userrepresentation));
       return $this->userAssembly->toRepresentation($newUser,$this->getPermissions($currentuser));
    }

    public function deleteUser($user_uid): void{
        $this->repository->deleteUser($user_uid);
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("USER",$user_uid);
    }
}
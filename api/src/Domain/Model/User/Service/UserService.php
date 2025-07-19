<?php

namespace App\Domain\Model\User\Service;

use App\common\Service\ServiceAbstract;
use App\Domain\Model\User\Repository\UserInfoRepository;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\User\Repository\UserRoleRepository;
use App\Domain\Model\User\Rest\UserAssembly;
use App\Domain\Model\User\Rest\UserInfoAssembly;
use App\Domain\Model\User\Rest\UserRepresentation;
use App\Domain\Model\User\Role;
use App\Domain\Model\User\User;
use App\Domain\Permission\PermissionRepository;

class UserService extends  ServiceAbstract
{

    /**
     * @var UserRepository
     */
    private UserRepository $repository;
    private $permissionrepository;
    private $userAssembly;
    private $userRoleRepository;
    private $userInfoRepository;
    private $userInfoAssembly;

    public function __construct(UserRepository $repository,
                                PermissionRepository $permissionRepository,
                                UserAssembly $userAssembly,
                                UserRoleRepository $userRoleRepository,
                                UserInfoRepository $userInfoRepository,UserInfoAssembly $userInfoAssembly)
    {
        $this->repository = $repository;
        $this->permissionrepository = $permissionRepository;
        $this->userAssembly = $userAssembly;
        $this->userRoleRepository = $userRoleRepository;
        $this->userInfoRepository = $userInfoRepository;
        $this->userInfoAssembly = $userInfoAssembly;
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

    public function updateUser($id, UserRepresentation $userrepresentation, string $currentUserUIDInSystem): ?UserRepresentation {
        // Validate roles based on current user permissions
        $this->validateUserRoles($userrepresentation->getRoles(), $currentUserUIDInSystem);
        
        // Update basic user information
        $user = $this->repository->updateUser($id, $this->userAssembly->toUser($userrepresentation));
        
        if (isset($user)) {
            // Update user info (phone, email)
            if($userrepresentation->getUserInfoRepresentation()) {
                $userinfo = $this->userInfoAssembly->toUserinfo($userrepresentation->getUserInfoRepresentation(), $id, false);
                if(isset($userinfo)){
                    $this->userInfoRepository->updateUserInfo($userinfo, $id);
                }
            }
            
            // Update user roles - delete existing roles and add new ones
            $this->userRoleRepository->deleteRoles($id);
            foreach ($userrepresentation->getRoles() as $row) {
                $role = new Role($row['id'],$row['role_name']);
                $this->userRoleRepository->createUser($role, $id);
            }
            
            return $this->userAssembly->toRepresentation($user,$this->getPermissions($currentUserUIDInSystem));
        }

        return null;
    }

    public function createUser(UserRepresentation $userrepresentation, string $currentuser): UserRepresentation {
        // Validate roles based on current user permissions
        $this->validateUserRoles($userrepresentation->getRoles(), $currentuser);
        
        $newUser = $this->repository->createUser($this->userAssembly->toUser($userrepresentation));

        if(isset($newUser)){
            $userinfo =  $this->userInfoAssembly->toUserinfo($userrepresentation->getUserInfoRepresentation(),$newUser->getId(),True);
            if(isset($userinfo)){
                $this->userInfoRepository->createUserInfo($userinfo, $newUser->getId());
            }
        }

        foreach ($userrepresentation->getRoles() as $row) {
            $role = new Role($row['id'],$row['role_name']);
            $this->userRoleRepository->createUser($role, $newUser->getId());
        }

        return $this->userAssembly->toRepresentation($newUser,$this->getPermissions($currentuser));
    }

    public function deleteUser($user_uid): void{
        // Delete related records first to avoid foreign key constraint violations
        
        // Delete user info records
        $this->userInfoRepository->deleteUserInfoForUser($user_uid);
        
        // Delete user role assignments  
        $this->userRoleRepository->deleteRoles($user_uid);
        
        // Finally delete the user
        $this->repository->deleteUser($user_uid);
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("USER",$user_uid);
    }

    /**
     * Validate that the current user can assign the specified roles
     * 
     * @param array $roles The roles to be assigned
     * @param string $currentUserUid The current user's UID
     * @throws \App\common\Exceptions\BrevetException If role assignment is not allowed
     */
    private function validateUserRoles(array $roles, string $currentUserUid): void
    {
        $userContext = \App\common\Context\UserContext::getInstance();
        
        // Superusers can assign any role
        if ($userContext->isSuperUser()) {
            return;
        }
        
        // Non-superusers can only assign these roles
        $allowedRoles = [
            \App\common\Context\UserContext::ROLE_ADMIN,
            \App\common\Context\UserContext::ROLE_USER,
            \App\common\Context\UserContext::ROLE_VOLONTEER
        ];
        
        foreach ($roles as $role) {
            $roleName = is_array($role) ? $role['role_name'] : $role;
            
            if (!in_array($roleName, $allowedRoles)) {
                throw new \App\common\Exceptions\BrevetException(
                    "Du har inte behörighet att tilldela rollen '$roleName'. Endast superanvändare kan tilldela denna roll.",
                    403
                );
            }
        }
    }
}
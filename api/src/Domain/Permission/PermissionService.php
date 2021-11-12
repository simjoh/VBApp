<?php

namespace App\Domain\Permission;

use Psr\Container\ContainerInterface;

class PermissionService
{


    public function __construct(ContainerInterface $c, PermissionRepository $permissionRepository)
    {
        $this->permissionrepository = $permissionRepository;
    }

    public function getPermissionsForUser(string $userUid): array{
            return $this->permissionrepository->getPermissionsFor($userUid);
    }

}
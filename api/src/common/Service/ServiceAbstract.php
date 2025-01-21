<?php

namespace App\common\Service;

use App\Action\CheckPoint\CheckpointAction;
use App\Domain\Permission\Permission;

abstract class ServiceAbstract
{

    abstract public function getPermissions($user_uid): array;


    function haspermission($permissions, $type): bool
    {
        foreach ($permissions as $x => $perm) {
            if ($type === "READ") {
                if ($perm->hasWritePermission()) {
                    return true;
                }
            }
            if ($type === "WRITE") {
                if ($perm->hasReadPermission()) {
                    return true;

                }
            }
            if ($type === "UPDATE") {
                if ($perm->hasUpdatePermission()) {
                    return true;

                }
            }
        }
        return false;
    }
}
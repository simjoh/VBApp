<?php

namespace App\Domain\Permission;

use Nette\Utils\Strings;

class Permission
{
    private int $id;
    private string $role;
    private string $type;
    private string $permissionto;

    /**
     * @param int $id
     * @param string $role
     */
    public function __construct(int $id, string $role, string $type, string $permissionto)
    {
        $this->id = $id;
        $this->role = $role;
        $this->type = $type;
        $this->permissionto = $permissionto;
    }

    public  function hasReadPermission(): bool
    {
        return Strings::compare('READ', $this->type);
    }
    public function hasWritePermission(): bool
    {
        return Strings::compare('WRITE', $this->type);
    }

    public function hasUpdatePermission(): bool
    {
        return Strings::compare('UPDATE', $this->type);
    }


}
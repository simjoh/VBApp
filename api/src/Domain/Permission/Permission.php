<?php

namespace App\Domain\Permission;

class Permission
{
    private int $id;
    private string $role;

    /**
     * @param int $id
     * @param string $role
     */
    public function __construct(int $id, string $role)
    {
        $this->id = $id;
        $this->role = $role;
    }

}
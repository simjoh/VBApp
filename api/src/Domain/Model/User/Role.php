<?php

namespace App\Domain\Model\User;

use JsonSerializable;

class Role implements JsonSerializable
{


    private int $id = 1;
    private string $role_name;

    /**
     * @param int $id
     * @param string $role_name
     */
    public function __construct(int $id, string $role_name)
    {
        $this->id = $id;
        $this->role_name = $role_name;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getRoleName(): string
    {
        return $this->role_name;
    }

    /**
     * @param string $role_name
     */
    public function setRoleName(string $role_name): void
    {
        $this->role_name = $role_name;
    }



    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}
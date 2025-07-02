<?php

namespace App\Domain\Model\User;


use JsonSerializable;

class User implements JsonSerializable
{
    private string $id;
    private string $givenname;
    private string $familyname;
    private string $username ="";
    private string $token;
    private  $roles = array();
    private string $password = '';


    public function __construct()
    {

    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getGivenname(): string
    {
        return $this->givenname;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getFamilyname(): string
    {
        return $this->familyname;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    public function setToken(string $token): void
    {
         $this->token = $token;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    /**
     * @param string $password
     */
    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return array
     */
    public function getRoles(): array
    {
        return $this->roles;
    }

    /**
     * @param array $roles
     */
    public function setRoles(array $roles): void
    {
        $this->roles = $roles;
    }

    /**
     * @param string $id
     */
    public function setId(string $id): void
    {
        $this->id = $id;
    }

    /**
     * @param string $givenname
     */
    public function setGivenname(string $givenname): void
    {
        $this->givenname = $givenname;
    }

    /**
     * @param string $familyname
     */
    public function setFamilyname(string $familyname): void
    {
        $this->familyname = $familyname;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }

    public function jsonSerialize(): string {
        return (object) get_object_vars($this);
    }
}
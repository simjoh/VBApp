<?php

namespace App\Domain\Model\Competitor;

class Competitor
{

    private string $id;
    private string $givenname;
    private string $familyname;
    private string $username;
    private string $token;
    private  $roles = array();




    /**
     * @param $givenname
     * @param $familyname
     * @param $token
     */
    public function __construct($id, $username ,$givenname, $familyname, $token)
    {
        $this->id = $id;
        $this->givenname = $givenname;
        $this->username = $username;
        $this->familyname = $familyname;
        $this->token = $token;
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
    public function getGivenname(): string
    {
        return $this->givenname;
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
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @param string $token
     */
    public function setToken(string $token): void
    {
        $this->token = $token;
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


}
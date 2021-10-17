<?php

namespace App\Domain\Model\User;

use App\common\Domain\Id;

class User extends Id
{

    private string $givenname;
    private string $familyname;
    private string $username;
    private string $token;

    /**
     * @param $givenname
     * @param $familyname
     * @param $token
     */
    public function __construct($id, $username ,$givenname, $familyname, $token)
    {
        parent::__construct($id);
        $this->givenname = $givenname;
        $this->username = $username;
        $this->familyname = $familyname;
        $this->token = $token;
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


}
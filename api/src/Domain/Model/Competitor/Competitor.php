<?php

namespace App\Domain\Model\Competitor;

use JsonSerializable;

class Competitor implements JsonSerializable
{

    private ?string $competitor_uid;
    private string $givenname = "";
    private string $familyname = "";
    private string $user_name;
    private string $token;
    private ?int $startnumber;
    private ?string $trackuid;

    private  $roles = array();

    public function __construct()
    {

    }

    /**
     * @param $givenname
     * @param $familyname
     * @param $token
     */
//    public function __construct($id, $username ,$givenname, $familyname, $token)
//    {
//        $this->id = $id;
//        $this->givenname = $givenname;
//        $this->username = $username;
//        $this->familyname = $familyname;
//        $this->token = $token;
//    }


    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->competitor_uid;
    }

    public function setId(): string
    {
        return $this->competitor_uid;
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
        return $this->user_name;
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

    /**
     * @return int
     */
    public function getStartnumber(): ?int
    {
        if(isset($this->startnumber)){
            return $this->startnumber;
        }

        return null;
    }

    /**
     * @param int $startnumber
     */
    public function setStartnumber(int $startnumber): void
    {
        $this->startnumber = $startnumber;
    }

    /**
     * @return string|null
     */
    public function getTrackuid(): ?string
    {
        return $this->trackuid;
    }

    /**
     * @param string|null $trackuid
     */
    public function setTrackuid(?string $trackuid): void
    {
        $this->trackuid = $trackuid;
    }


    public function jsonSerialize(): mixed {
        return (object) get_object_vars($this);
    }


}
<?php

namespace App\Domain\Model\User\Rest;

use App\common\Rest\Link;

class UserRepresentation implements \JsonSerializable
{

    private string $user_uid;
    private string $givenname;
    private string $familyname;
    private string $username;

    /**
     * @return string
     */
    public function getUserUid(): string
    {
        return $this->user_uid;
    }

    /**
     * @param string $user_uid
     */
    public function setUserUid(string $user_uid): void
    {
        $this->user_uid = $user_uid;
    }

    /**
     * @return string
     */
    public function getGivenname(): string
    {
        return $this->givenname;
    }

    /**
     * @param string $givenname
     */
    public function setGivenname(string $givenname): void
    {
        $this->givenname = $givenname;
    }

    /**
     * @return string
     */
    public function getFamilyname(): string
    {
        return $this->familyname;
    }

    /**
     * @param string $familyname
     */
    public function setFamilyname(string $familyname): void
    {
        $this->familyname = $familyname;
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @param string $username
     */
    public function setUsername(string $username): void
    {
        $this->username = $username;
    }


    /**
     * @return Link|null
     */
    public function getLink(): ?Link
    {
        return $this->link;
    }

    /**
     * @param Link|null $link
     */
    public function setLink(?Link $link): void
    {
        $this->link = $link;
    }
    private string $token;
    private ?Link $link;






    public function jsonSerialize()
    {
        return (object) get_object_vars($this);
    }
}
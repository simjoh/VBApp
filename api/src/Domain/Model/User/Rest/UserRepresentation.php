<?php

namespace App\Domain\Model\User\Rest;

use App\common\Rest\Link;

class UserRepresentation implements \JsonSerializable
{

    private string $user_uid;
    private string $givenname;
    private string $familyname;
    private string $username;
    private  $roles = array();
    private ?Link $link = null;
    private $links = array();
    private ?UserInfoRepresentation $userInfoRepresentation = null;
    private ?string $password = null;
    private ?int $organizer_id = null;



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

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    /**
     * @return UserInfoRepresentation|null
     */
    public function getUserInfoRepresentation(): ?UserInfoRepresentation
    {
        return $this->userInfoRepresentation;
    }

    /**
     * @param UserInfoRepresentation|null $userInfoRepresentation
     */
    public function setUserInfoRepresentation(?UserInfoRepresentation $userInfoRepresentation): void
    {
        $this->userInfoRepresentation = $userInfoRepresentation;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(?string $password): void
    {
        $this->password = $password;
    }

    /**
     * @return int|null
     */
    public function getOrganizerId(): ?int
    {
        return $this->organizer_id;
    }

    /**
     * @param int|null $organizer_id
     */
    public function setOrganizerId(?int $organizer_id): void
    {
        $this->organizer_id = $organizer_id;
    }

    public function jsonSerialize(): mixed
    {
        $data = [
            'user_uid' => $this->user_uid,
            'givenname' => $this->givenname,
            'familyname' => $this->familyname,
            'username' => $this->username,
            'roles' => $this->roles,
            'link' => $this->link,
            'links' => $this->links,
            'userInfoRepresentation' => $this->userInfoRepresentation,
            'password' => $this->password,
            'organizer_id' => $this->organizer_id  // Use snake_case for frontend compatibility
        ];
        return (object) $data;
    }
}
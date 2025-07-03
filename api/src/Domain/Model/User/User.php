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
    private ?\DateTime $createdAt = null;
    private ?\DateTime $updatedAt = null;
    private bool $confirmed = false;
    private ?\DateTime $confirmedAt = null;
    private ?int $organizerId = null;


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

    /**
     * @return \DateTime|null
     */
    public function getCreatedAt(): ?\DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime|null $createdAt
     */
    public function setCreatedAt(?\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getUpdatedAt(): ?\DateTime
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime|null $updatedAt
     */
    public function setUpdatedAt(?\DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * @return bool
     */
    public function isConfirmed(): bool
    {
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     */
    public function setConfirmed(bool $confirmed): void
    {
        $this->confirmed = $confirmed;
    }

    /**
     * @return \DateTime|null
     */
    public function getConfirmedAt(): ?\DateTime
    {
        return $this->confirmedAt;
    }

    /**
     * @param \DateTime|null $confirmedAt
     */
    public function setConfirmedAt(?\DateTime $confirmedAt): void
    {
        $this->confirmedAt = $confirmedAt;
    }

    /**
     * @return int|null
     */
    public function getOrganizerId(): ?int
    {
        return $this->organizerId;
    }

    /**
     * @param int|null $organizerId
     */
    public function setOrganizerId(?int $organizerId): void
    {
        $this->organizerId = $organizerId;
    }

    public function jsonSerialize(): mixed {
        $data = [
            'id' => $this->id,
            'givenname' => $this->givenname,
            'familyname' => $this->familyname,
            'username' => $this->username,
            'token' => $this->token,
            'roles' => $this->roles,
            'password' => $this->password,
            'createdAt' => $this->createdAt,
            'updatedAt' => $this->updatedAt,
            'confirmed' => $this->confirmed,
            'confirmedAt' => $this->confirmedAt,
            'organizer_id' => $this->organizerId  // Use snake_case for frontend compatibility
        ];
        return (object) $data;
    }
}
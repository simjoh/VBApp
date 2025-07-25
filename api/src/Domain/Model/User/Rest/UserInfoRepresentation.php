<?php

namespace App\Domain\Model\User\Rest;

use JsonSerializable;

class UserInfoRepresentation  implements JsonSerializable
{

    private ?string $user_uid = null;
    private ?string $uid = null;
    private string $phone = '';
    private string $email = '';
    private ?array $link;


    /**
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->uid;
    }

    /**
     * @param string|null $uid
     */
    public function setUid(?string $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string|null
     */
    public function getUserUid(): ?string
    {
        return $this->user_uid;
    }

    /**
     * @param string|null $user_uid
     */
    public function setUserUid(?string $user_uid): void
    {
        $this->user_uid = $user_uid;
    }

    /**
     * @return array|null
     */
    public function getLink(): ?array
    {
        return $this->link;
    }

    /**
     * @param array|null $link
     */
    public function setLink(?array $link): void
    {
        $this->link = $link;
    }



    public function jsonSerialize(): mixed {
        return (object) get_object_vars($this);
    }


}
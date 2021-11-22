<?php

namespace App\Domain\Model\User;

use JsonSerializable;
use Serializable;

class UserInfo
{

    private string $uid;
    private string $user_uid;
    private string $phone;
    private string $email;

    /**
     * @param string $uid
     * @param string $phone
     * @param string $email
     */
    public function __construct(string $user_uid, string $uid, string $phone, string $email)
    {
        $this->uid = $uid;
        $this->phone = $phone;
        $this->email = $email;
        $this->user_uid = $user_uid;
    }

    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     */
    public function setUid(string $uid): void
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




}
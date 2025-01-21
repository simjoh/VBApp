<?php

namespace App\Domain\Model\Organizer;

use PrestaShop\Decimal\DecimalNumber;

class Organizer
{
    private $organizer_id;
    private $name;
    private $contact_person;
    private $email;
    private $phone;
    private $created_at;
    private $updated_at;

    // Constructor
    public function __construct($organizer_id, $name,  $contact_person ,$email, $phone = null, $created_at = null, $updated_at = null)
    {
        $this->organizer_id = $organizer_id;
        $this->name = $name;
        $this->contact_person = $contact_person;
        $this->email = $email;
        $this->phone = $phone;
        $this->created_at = $created_at;
        $this->updated_at = $updated_at;
    }

    // Getters
    public function getOrganizerId()
    {
        return $this->organizer_id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function getPhone()
    {
        return $this->phone;
    }

    public function getCreatedAt()
    {
        return $this->created_at;
    }

    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    // Setters
    public function setOrganizerId($organizer_id)
    {
        $this->organizer_id = $organizer_id;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed|null
     */
    public function getContactPerson()
    {
        return $this->contact_person;
    }

    /**
     * @param mixed|null $contact_person
     */
    public function setContactPerson($contact_person): void
    {
        $this->contact_person = $contact_person;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    public function setCreatedAt($created_at)
    {
        $this->created_at = $created_at;
    }

    public function setUpdatedAt($updated_at)
    {
        $this->updated_at = $updated_at;
    }

}
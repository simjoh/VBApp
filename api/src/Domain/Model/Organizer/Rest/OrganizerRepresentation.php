<?php

namespace App\Domain\Model\Organizer\Rest;

use JsonSerializable;

class OrganizerRepresentation  implements JsonSerializable
{

    private $organizer_id;
    private $name;
    private $contact_person;
    private $email;
    private $phone;

    private array $links = [];

    /**
     * @return mixed
     */
    public function getOrganizerId()
    {
        return $this->organizer_id;
    }

    /**
     * @param mixed $organizer_id
     */
    public function setOrganizerId($organizer_id): void
    {
        $this->organizer_id = $organizer_id;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getContactPerson()
    {
        return $this->contact_person;
    }

    /**
     * @param mixed $contact_person
     */
    public function setContactPerson($contact_person): void
    {
        $this->contact_person = $contact_person;
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }

    /**
     * @return mixed
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * @param mixed $phone
     */
    public function setPhone($phone): void
    {
        $this->phone = $phone;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    public function jsonSerialize(): mixed
    {
        return (object)get_object_vars($this);
    }
}
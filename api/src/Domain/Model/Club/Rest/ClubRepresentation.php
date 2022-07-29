<?php

namespace App\Domain\Model\Club\Rest;

use JsonSerializable;

class ClubRepresentation implements JsonSerializable
{

    private $club_uid;
    private $title;
    private array $links = [];



    /**
     * @return mixed
     */
    public function getClubUid()
    {
        return $this->club_uid;
    }

    /**
     * @param mixed $club_uid
     */
    public function setClubUid($club_uid): void
    {
        $this->club_uid = $club_uid;
    }

    /**
     * @return mixed
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * @param mixed $title
     */
    public function setTitle($title): void
    {
        $this->title = $title;
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


    public function jsonSerialize()
    {
        return (object)get_object_vars($this);
    }
}
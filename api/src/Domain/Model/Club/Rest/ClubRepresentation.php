<?php

namespace App\Domain\Model\Club\Rest;

use JsonSerializable;

class ClubRepresentation implements JsonSerializable
{

    private string $club_uid;
    private string $title;
    private $acp_code;
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

    /**
     * @return mixed
     */
    public function getAcpCode()
    {
        return $this->acp_code;
    }

    /**
     * @param mixed $acp_code
     */
    public function setAcpCode($acp_code): void
    {
        $this->acp_code = $acp_code;
    }


    public function jsonSerialize()
    {
        return (object)get_object_vars($this);
    }
}
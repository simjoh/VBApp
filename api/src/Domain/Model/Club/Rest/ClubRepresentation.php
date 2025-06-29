<?php

namespace App\Domain\Model\Club\Rest;

use JsonSerializable;

class ClubRepresentation implements JsonSerializable
{

    private ?string $club_uid = null;
    private ?string $title = null;
    private ?string $acp_code = null;
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
     * @return string|null
     */
    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @param string|null $title
     */
    public function setTitle(?string $title): void
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
     * @return string|null
     */
    public function getAcpCode(): ?string
    {
        return $this->acp_code;
    }

    /**
     * @param string|null $acp_code
     */
    public function setAcpCode(?string $acp_code): void
    {
        $this->acp_code = $acp_code;
    }


    public function jsonSerialize(): mixed
    {
        return (object)[
            'club_uid' => $this->club_uid,
            'title' => $this->title,
            'acp_code' => $this->acp_code,
            'links' => $this->links
        ];
    }
}
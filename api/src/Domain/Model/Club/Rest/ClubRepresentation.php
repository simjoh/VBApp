<?php

namespace App\Domain\Model\Club\Rest;

use JsonSerializable;

class ClubRepresentation implements JsonSerializable
{

    private string $club_uid;
    private ?string $title = null;
    private ?string $acp_kod = null;
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
        return $this->acp_kod;
    }

    /**
     * @param string|null $acp_kod
     */
    public function setAcpCode(?string $acp_kod): void
    {
        $this->acp_kod = $acp_kod;
    }


    public function jsonSerialize(): mixed
    {
        return (object)[
            'club_uid' => $this->club_uid,
            'title' => $this->title,
            'acp_code' => $this->acp_kod,  // Output as acp_code for frontend compatibility
            'links' => $this->links
        ];
    }
}
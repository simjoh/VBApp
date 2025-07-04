<?php

namespace App\Domain\Model\Club;

use PrestaShop\Decimal\DecimalNumber;

class Club
{

    private ?string $club_uid = null;
    private ?string $acp_kod = null;
    private ?string $title = null;

    /**
     * @return string
     */
    public function getClubUid(): string
    {
        return $this->club_uid;
    }

    /**
     * @param string $club_uid
     */
    public function setClubUid(string $club_uid): void
    {
        $this->club_uid = $club_uid;
    }

    /**
     * @return string|null
     */
    public function getAcpKod(): ?string
    {
        return $this->acp_kod;
    }

    /**
     * @param string|null $acp_kod
     */
    public function setAcpKod(?string $acp_kod): void
    {
        $this->acp_kod = $acp_kod;
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

}
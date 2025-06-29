<?php

namespace App\Domain\Model\Club;

use PrestaShop\Decimal\DecimalNumber;

class Club
{

    private ?string $club_uid = null;
    private ?string $acp_code = null;
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
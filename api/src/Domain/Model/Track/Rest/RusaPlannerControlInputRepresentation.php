<?php

namespace App\Domain\Model\Track\Rest;

use App\Domain\Model\Site\Rest\SiteRepresentation;
use JsonSerializable;

class RusaPlannerControlInputRepresentation implements JsonSerializable
{

    private string $DISTANCE;
    private string $SITE;


    /**
     * @return string
     */
    public function getDISTANCE(): string
    {
        return $this->DISTANCE;
    }

    /**
     * @param string $DISTANCE
     */
    public function setDISTANCE(string $DISTANCE): void
    {
        $this->DISTANCE = $DISTANCE;
    }

    /**
     * @return SiteRepresentation
     */
    public function getSITE(): string
    {
        return $this->SITE;
    }

    /**
     * @param SiteRepresentation $SITE
     */
    public function setSITE(string $SITE): void
    {
        $this->SITE = $SITE;
    }

    public function jsonSerialize(): mixed
    {
        return (object) get_object_vars($this);
    }
}
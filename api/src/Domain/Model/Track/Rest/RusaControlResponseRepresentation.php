<?php

namespace App\Domain\Model\Track\Rest;

use App\Domain\Model\Site\Rest\SiteRepresentation;
use JsonSerializable;

class RusaControlResponseRepresentation implements JsonSerializable
{

   private SiteRepresentation $siteRepresentation;
   private RusaControlRepresentation $rusaControlRepresentation;

    /**
     * @return SiteRepresentation
     */
    public function getSiteRepresentation(): SiteRepresentation
    {
        return $this->siteRepresentation;
    }

    /**
     * @param SiteRepresentation $siteRepresentation
     */
    public function setSiteRepresentation(SiteRepresentation $siteRepresentation): void
    {
        $this->siteRepresentation = $siteRepresentation;
    }

    /**
     * @return RusaControlRepresentation
     */
    public function getRusaControlRepresentation(): RusaControlRepresentation
    {
        return $this->rusaControlRepresentation;
    }

    /**
     * @param RusaControlRepresentation $rusaControlRepresentation
     */
    public function setRusaControlRepresentation(RusaControlRepresentation $rusaControlRepresentation): void
    {
        $this->rusaControlRepresentation = $rusaControlRepresentation;
    }

    public function jsonSerialize(): mixed
    {
        return (object) get_object_vars($this);
    }

}
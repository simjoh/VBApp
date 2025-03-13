<?php

namespace App\Domain\Model\CheckPoint\Rest;


use App\common\Rest\Link;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Model\Site\Site;
use JsonSerializable;

class CheckpointRepresentation implements JsonSerializable
{

    private ?string $checkpoint_uid = null;
    private ?SiteRepresentation $site;
    private ?string $title = "";
    private ?string $description = "";
    private ?float $distance = 0;
    private $opens = "";
    private $closing = "";
    private ?Link $link;



    /**
     * @return string|null
     */
    public function getCheckpointUid(): ?string
    {
        return $this->checkpoint_uid;
    }

    /**
     * @param string $checkpoint_uid
     */
    public function setCheckpointUid(string $checkpoint_uid): void
    {
        $this->checkpoint_uid = $checkpoint_uid;
    }

    /**
     * @return SiteRepresentation
     */
    public function getSite(): SiteRepresentation
    {
        return $this->site;
    }

    /**
     * @param SiteRepresentation $site
     */
    public function setSite(SiteRepresentation $site): void
    {
        $this->site = $site;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return float
     */
    public function getDistance(): float
    {
        return $this->distance;
    }

    /**
     * @param float $distance
     */
    public function setDistance(float $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @return mixed
     */
    public function getOpens()
    {
        return $this->opens;
    }

    /**
     * @param mixed $opens
     */
    public function setOpens($opens): void
    {
        $this->opens = $opens;
    }

    /**
     * @return string
     */
    public function getClosing(): string
    {
        return $this->closing;
    }

    /**
     * @param string $closing
     */
    public function setClosing(string $closing): void
    {
        $this->closing = $closing;
    }

    /**
     * @return Link|null
     */
    public function getLink(): ?Link
    {
        return $this->link;
    }

    /**
     * @param Link|null $link
     */
    public function setLink(?Link $link): void
    {
        $this->link = $link;
    }



    public function jsonSerialize(): mixed {
        return (object) get_object_vars($this);
    }
}
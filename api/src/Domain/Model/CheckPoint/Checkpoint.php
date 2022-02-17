<?php

namespace App\Domain\Model\CheckPoint;

class Checkpoint
{
    private string $checkpoint_uid;
    private ?string $site_uid;
    private ?string $title;
    private ?string $description;
    private ?float $distance;
    private $opens;
    private $closing;
    /**
     * @return string
     */
    public function getCheckpointUid(): string
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
     * @return string
     */
    public function getSiteUid(): string
    {
        return $this->site_uid;
    }

    /**
     * @param string $site_uid
     */
    public function setSiteUid(string $site_uid): void
    {
        $this->site_uid = $site_uid;
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
     * @return mixed
     */
    public function getClosing()
    {
        return $this->closing;
    }

    /**
     * @param mixed $closing
     */
    public function setClosing($closing): void
    {
        $this->closing = $closing;
    }

    public function __construct()
    {
    }


}
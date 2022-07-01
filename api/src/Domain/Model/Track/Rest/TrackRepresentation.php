<?php

namespace App\Domain\Model\Track\Rest;

use App\common\Rest\Link;
use JsonSerializable;

class TrackRepresentation implements JsonSerializable
{

    private string $title;
    private string $descriptions;
    private array $checkpoints;
    private string $linktotrack;
    private ?string $heightdifference;
    private string $distance;
    private String $track_uid;
    private string $event_uid;
    private string $start_date_time;
    private ?bool $active;
    private ?Link $link;

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
    public function getDescriptions(): string
    {
        return $this->descriptions;
    }

    /**
     * @param string $descriptions
     */
    public function setDescriptions(string $descriptions): void
    {
        $this->descriptions = $descriptions;
    }

    /**
     * @return array
     */
    public function getCheckpoints(): array
    {
        if(empty($this->checkpoints)){
            return array();
        } else {
            return $this->checkpoints;
        }

    }

    /**
     * @param array $checkpoints
     */
    public function setCheckpoints(array $checkpoints): void
    {
        $this->checkpoints = $checkpoints;
    }


    /**
     * @return string
     */
    public function getLinktotrack(): string
    {
        return $this->linktotrack;
    }

    /**
     * @param string $linktotrack
     */
    public function setLinktotrack(string $linktotrack): void
    {
        $this->linktotrack = $linktotrack;
    }
    /**
     * @return string|null
     */
    public function getHeightdifference(): ?string
    {
        return $this->heightdifference;
    }

    /**
     * @param string|null $heightdifference
     */
    public function setHeightdifference(?string $heightdifference): void
    {
        $this->heightdifference = $heightdifference;
    }


    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }

    /**
     * @return string
     */
    public function getDistance(): string
    {
        return $this->distance;
    }

    /**
     * @return String
     */
    public function getTrackUid(): string
    {
        return $this->track_uid;
    }

    /**
     * @param String $track_uid
     */
    public function setTrackUid(string $track_uid): void
    {
        $this->track_uid = $track_uid;
    }
    /**
     * @return string
     */
    public function getEventUid(): string
    {
        return $this->event_uid;
    }

    /**
     * @param string $event_uid
     */
    public function setEventUid(string $event_uid): void
    {
        $this->event_uid = $event_uid;
    }




    /**
     * @param string $distance
     */
    public function setDistance(string $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @return string
     */
    public function getStartDateTime(): string
    {
        return $this->start_date_time;
    }

    /**
     * @return bool|null
     */
    public function getActive(): ?bool
    {
        return $this->active;
    }

    /**
     * @param bool|null $active
     */
    public function setActive(?bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @param string $start_date_time
     */
    public function setStartDateTime(string $start_date_time): void
    {
        $this->start_date_time = $start_date_time;
    }
}
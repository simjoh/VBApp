<?php

namespace App\Domain\Model\Track\Rest;

use JsonSerializable;

class RusaPlannerInputRepresentation implements JsonSerializable
{

    private ?array $controls;
    private ?string $event_distance = "";
    private ?string $start_date = "";
    private ? string $start_time = "";
    private ?string $event_uid = "";
    private ?string $track_title = "";
    private ?string $link = "";

    /**
     * @return string|null
     */
    public function getLink(): ?string
    {
        return $this->link;
    }

    /**
     * @param string|null $link
     */
    public function setLink(?string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return string|null
     */
    public function getTrackTitle(): ?string
    {
        return $this->track_title;
    }

    /**
     * @param string|null $track_title
     */
    public function setTrackTitle(?string $track_title): void
    {
        $this->track_title = $track_title;
    }



    /**
     * @return mixed
     */
    public function getEventUid()
    {
        return $this->event_uid;
    }

    /**
     * @param mixed $event_uid
     */
    public function setEventUid($event_uid): void
    {
        $this->event_uid = $event_uid;
    }

    /**
     * @return array
     */
    public function getControls(): array
    {
        return $this->controls;
    }

    /**
     * @param array $controls
     */
    public function setControls(array $controls): void
    {
        $this->controls = $controls;
    }

    /**
     * @return mixed
     */
    public function getEventDistance()
    {
        return $this->event_distance;
    }

    /**
     * @param mixed $event_distance
     */
    public function setEventDistance($event_distance): void
    {
        $this->event_distance = $event_distance;
    }

    /**
     * @return mixed
     */
    public function getStartDate()
    {
        return $this->start_date;
    }

    /**
     * @param mixed $start_date
     */
    public function setStartDate($start_date): void
    {
        $this->start_date = $start_date;
    }

    /**
     * @return mixed
     */
    public function getStartTime()
    {
        return $this->start_time;
    }

    /**
     * @param mixed $start_time
     */
    public function setStartTime($start_time): void
    {
        $this->start_time = $start_time;
    }

    public function jsonSerialize()
    {
        return (object) get_object_vars($this);
    }
}
<?php

namespace App\Domain\Model\Randonneur\Rest;

use JsonSerializable;

class TrackInfoRepresentation implements JsonSerializable
{

    private string $distance;
    private string $start_date_time;

    /**
     * @return string
     */
    public function getDistance(): string
    {
        return $this->distance;
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
     * @param string $start_date_time
     */
    public function setStartDateTime(string $start_date_time): void
    {
        $this->start_date_time = $start_date_time;
    }


    public function jsonSerialize()
    {
        return (object)get_object_vars($this);
    }

}
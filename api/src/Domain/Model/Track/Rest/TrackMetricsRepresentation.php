<?php

namespace App\Domain\Model\Track\Rest;

use JsonSerializable;

class TrackMetricsRepresentation implements JsonSerializable

{
    private string $countParticipants = "0";
    private string $countDnf = "0";
    private string $countDns = "0";
    private string $countFinished = "0";

    /**
     * @return string
     */
    public function getCountParticipants(): string
    {
        return $this->countParticipants;
    }

    /**
     * @param string $countParticipants
     */
    public function setCountParticipants(string $countParticipants): void
    {
        $this->countParticipants = $countParticipants;
    }

    /**
     * @return string
     */
    public function getCountDnf(): string
    {
        return $this->countDnf;
    }

    /**
     * @param string $countDnf
     */
    public function setCountDnf(string $countDnf): void
    {
        $this->countDnf = $countDnf;
    }

    /**
     * @return string
     */
    public function getCountDns(): string
    {
        return $this->countDns;
    }

    /**
     * @param string $countDns
     */
    public function setCountDns(string $countDns): void
    {
        $this->countDns = $countDns;
    }

    /**
     * @return string
     */
    public function getCountFinished(): string
    {
        return $this->countFinished;
    }

    /**
     * @param string $countFinished
     */
    public function setCountFinished(string $countFinished): void
    {
        $this->countFinished = $countFinished;
    }


    public function jsonSerialize()
    {
        return (object) get_object_vars($this);
    }

}
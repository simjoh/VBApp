<?php

namespace App\Domain\Model\Event\Rest;

use JsonSerializable;

class EventMetricRepresentation implements JsonSerializable
{
    private string $countParticipants;
    private string $countDnf;
    private string $countDns;
    private string $countFinished;

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


    public function jsonSerialize(): mixed
    {
        return (object)get_object_vars($this);
    }

}
<?php

namespace App\Domain\Model\Event\Rest;


use JsonSerializable;

class EventInformationRepresentation implements JsonSerializable
{
    private EventRepresentation $event;
    private array $tracks = [];
    /**
     * @return EventRepresentation
     */
    public function getEvent(): EventRepresentation
    {
        return $this->event;
    }

    /**
     * @param EventRepresentation $event
     */
    public function setEvent(EventRepresentation $event): void
    {
        $this->event = $event;
    }

    /**
     * @return array
     */
    public function getTracks(): array
    {
        return $this->tracks;
    }

    /**
     * @param array $tracks
     */
    public function setTracks(array $tracks): void
    {
        $this->tracks = $tracks;
    }


    public function jsonSerialize()
    {
        return (object) get_object_vars($this);
    }
}
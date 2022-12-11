<?php

namespace App\Domain\Model\Track\Rest;

use App\Domain\Model\Event\Rest\EventRepresentation;
use JsonSerializable;

class RusaPlannerResponseRepresentation implements JsonSerializable
{

    private RusaMetaRepresentation $rusaMetaRepresentation;
    private EventRepresentation $eventRepresentation;
    private array $rusaplannercontrols;
    private RusaTrackRepresentation $rusaTrackRepresentation;

    /**
     * @return RusaMetaRepresentation
     */
    public function getRusaMetaRepresentation(): RusaMetaRepresentation
    {
        return $this->rusaMetaRepresentation;
    }

    /**
     * @param RusaMetaRepresentation $rusaMetaRepresentation
     */
    public function setRusaMetaRepresentation(RusaMetaRepresentation $rusaMetaRepresentation): void
    {
        $this->rusaMetaRepresentation = $rusaMetaRepresentation;
    }


    /**
     * @return EventRepresentation
     */
    public function getEventRepresentation(): EventRepresentation
    {
        return $this->eventRepresentation;
    }

    /**
     * @param EventRepresentation $eventRepresentation
     */
    public function setEventRepresentation(EventRepresentation $eventRepresentation): void
    {
        $this->eventRepresentation = $eventRepresentation;
    }
    /**
     * @return RusaTrackRepresentation
     */
    public function getRusaTrackRepresentation(): RusaTrackRepresentation
    {
        return $this->rusaTrackRepresentation;
    }

    /**
     * @param RusaTrackRepresentation $rusaTrackRepresentation
     */
    public function setRusaTrackRepresentation(RusaTrackRepresentation $rusaTrackRepresentation): void
    {
        $this->rusaTrackRepresentation = $rusaTrackRepresentation;
    }


    /**
     * @return array
     */
    public function getRusaplannercontrols(): array
    {
        return $this->rusaplannercontrols;
    }

    /**
     * @param array $rusaplannercontrols
     */
    public function setRusaplannercontrols(array $rusaplannercontrols): void
    {
        $this->rusaplannercontrols = $rusaplannercontrols;
    }

    public function jsonSerialize(): mixed
    {
        return (object) get_object_vars($this);
    }
}
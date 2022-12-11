<?php

namespace App\Domain\Model\Track\Rest;


use JsonSerializable;

class TrackInformationRepresentation implements JsonSerializable
{
    private TrackMetricsRepresentation $trackMetricsRepresentation;
    private TrackRepresentation $trackRepresentation;


    /**
     * @return TrackMetricsRepresentation
     */
    public function getTrackMetricsRepresentation(): TrackMetricsRepresentation
    {
        return $this->trackMetricsRepresentation;
    }

    /**
     * @param TrackMetricsRepresentation $trackMetricsRepresentation
     */
    public function setTrackMetricsRepresentation(TrackMetricsRepresentation $trackMetricsRepresentation): void
    {
        $this->trackMetricsRepresentation = $trackMetricsRepresentation;
    }

    /**
     * @return TrackRepresentation
     */
    public function getTrackRepresentation(): TrackRepresentation
    {
        return $this->trackRepresentation;
    }

    /**
     * @param TrackRepresentation $trackRepresentation
     */
    public function setTrackRepresentation(TrackRepresentation $trackRepresentation): void
    {
        $this->trackRepresentation = $trackRepresentation;
    }

    public function jsonSerialize(): mixed
    {
        return (object) get_object_vars($this);
    }
}
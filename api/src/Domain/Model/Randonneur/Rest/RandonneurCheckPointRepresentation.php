<?php

namespace App\Domain\Model\Randonneur\Rest;

use App\Domain\Model\CheckPoint\Checkpoint;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
use App\Domain\Model\Track\Rest\TrackRepresentation;
use JsonSerializable;

class RandonneurCheckPointRepresentation implements JsonSerializable
{

    private CheckpointRepresentation $checkpoint;
    private ?bool $active;


    private array $links = [];

    /**
     * @return CheckpointRepresentation
     */
    public function getCheckpoint(): CheckpointRepresentation
    {
        return $this->checkpoint;
    }

    /**
     * @param CheckpointRepresentation $checkpoint
     */
    public function setCheckpoint(CheckpointRepresentation $checkpoint): void
    {
        $this->checkpoint = $checkpoint;
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
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links): void
    {
        $this->links = $links;
    }


    public function jsonSerialize()
    {
        return (object)get_object_vars($this);
    }
}
<?php

namespace App\Domain\Model\Randonneur\Rest;

use App\Domain\Model\CheckPoint\Checkpoint;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
use JsonSerializable;

class RandonneurCheckPointRepresentation implements JsonSerializable
{

    private CheckpointRepresentation $checkpoint;
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
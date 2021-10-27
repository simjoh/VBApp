<?php

namespace App\Domain\Model\Track;

use JsonSerializable;

class Track implements JsonSerializable
{
   private string $track_uid;
   private string $title;
   private string $link;
   private string $heightdifference;
   private string $event_uid;
   private string $description;
   private $distance;
   private array $checkpoints;


    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }

}

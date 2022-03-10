<?php

namespace App\Domain\Model\Track;

use App\Domain\Model\Event\Event;

class TracksForEvents
{

    public Event $event;
    public array $sites;
    public array $checkpoints = array();
    public Track $track;
    public array $tracks = array();

}
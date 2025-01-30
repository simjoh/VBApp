<?php

namespace App\Domain\Model\Loppservice\Rest\LoppserviceRepresentations;


class Event
{
    public string $event_uid;
    public string $title;
    public string $description;
    public string $startdate;
    public string $enddate;
    public int $completed;
    public string $created_at;
    public string $updated_at;
    public string $embedid;
    public string $event_type;
    public EventConfiguration $eventconfiguration;

    public function __construct(array $data)
    {
        $this->event_uid = $data['event_uid'];
        $this->title = $data['title'];
        $this->description = $data['description'];
        $this->startdate = $data['startdate'];
        $this->enddate = $data['enddate'];
        $this->completed = $data['completed'];
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'];
        $this->embedid = $data['embedid'];
        $this->event_type = $data['event_type'];
        $this->eventconfiguration = new EventConfiguration($data['eventconfiguration']);
    }
}
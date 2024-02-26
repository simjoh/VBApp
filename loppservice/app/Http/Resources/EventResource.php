<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;
class EventResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'event_uid' => $this->event_uid,
            'title' => $this->title,
            'description' => $this->description,
            'startdate' => $this->startdate,
            'enddate' => $this->enddate,
            'completed' => $this->completed,
            'eventconfiguration' => $this->eventconfiguration
        ];
    }
}

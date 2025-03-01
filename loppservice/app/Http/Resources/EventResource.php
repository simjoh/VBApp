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
            'event_type' => $this->event_type,
            'organizer_id' => $this->organizer_id,
            'county_id' => $this->county_id,
            'event_group_uid' => $this->event_group_uid,
            'eventconfiguration' => $this->eventconfiguration,
            'route_detail' => $this->routeDetail
        ];
    }
}

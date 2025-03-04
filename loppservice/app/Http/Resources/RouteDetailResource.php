<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RouteDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'event_uid' => $this->event_uid,
            'distance' => $this->distance,
            'height_difference' => $this->height_difference,
            'start_time' => $this->start_time,
            'start_place' => $this->start_place,
            'name' => $this->name,
            'description' => $this->description,
            'track_link' => $this->track_link,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // HATEOAS links
            'links' => [
                'self' => route('api.events.route_details', ['event_uid' => $this->event_uid]),
                'update' => route('api.events.update_route_details', ['event_uid' => $this->event_uid]),
                'event' => route('api.events.show', ['eventUid' => $this->event_uid]),
            ],
        ];
    }
}

<?php
namespace App\Http\Resources;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
            'eventconfiguration' => $this->eventConfiguration,
            'route_detail' => $this->routeDetail,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // HATEOAS links
            'links' => [
                'self' => route('api.events.show', ['eventUid' => $this->event_uid]),
                'update' => route('api.events.update', ['eventUid' => $this->event_uid]),
                'delete' => route('api.events.delete', ['eventUid' => $this->event_uid]),
                'route_details' => route('api.events.route_details', ['event_uid' => $this->event_uid]),
            ],

            // Related resources links
            'related' => [
                'organizer' => $this->when($this->organizer_id, function() {
                    return route('api.organizers.show', ['id' => $this->organizer_id]);
                }),
                'event_group' => $this->when($this->event_group_uid, function() {
                    return route('api.event_groups.show', ['uid' => $this->event_group_uid]);
                }),
                'registrations' => route('api.events.registrations', ['eventUid' => $this->event_uid]),
                'startlist' => url("/startlist/event/{$this->event_uid}/showall"),
            ],
        ];
    }
}

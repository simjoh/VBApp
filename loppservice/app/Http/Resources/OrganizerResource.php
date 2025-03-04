<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrganizerResource extends JsonResource
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
            'organization_name' => $this->organization_name,
            'description' => $this->description,
            'website' => $this->website,
            'logo_svg' => $this->logo_svg ? base64_encode($this->logo_svg) : null,
            'contact_person_name' => $this->contact_person_name,
            'email' => $this->email,
            'active' => $this->active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            // Include links for HATEOAS compliance
            'links' => [
                'self' => route('api.organizers.show', ['id' => $this->id]),
                'events' => route('api.organizers.events', ['id' => $this->id]),
            ],
        ];
    }
}

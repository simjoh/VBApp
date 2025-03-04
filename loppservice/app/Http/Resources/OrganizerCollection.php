<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class OrganizerCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param  Request  $request
     * @return array
     */
    public function toArray($request): array
    {
        $resource = $this->resource;

        return [
            'data' => $this->collection,
            'links' => [
                'self' => route('api.organizers.index'),
                'first' => $resource->url(1),
                'last' => $resource->url($resource->lastPage()),
                'prev' => $resource->previousPageUrl(),
                'next' => $resource->nextPageUrl(),
            ],
            'meta' => [
                'current_page' => $resource->currentPage(),
                'from' => $resource->firstItem(),
                'last_page' => $resource->lastPage(),
                'path' => $resource->path(),
                'per_page' => $resource->perPage(),
                'to' => $resource->lastItem(),
                'total' => $resource->total(),
                'active_count' => $this->collection->where('active', true)->count(),
                'version' => '1.0',
            ],
        ];
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * RouteDetail Model
 *
 * This model represents the detailed information about a route for an event.
 * Each Event has exactly one RouteDetail (one-to-one relationship).
 *
 * Usage examples:
 *
 * // Create a route detail for an event
 * $event = Event::find($eventUid);
 * $event->routeDetail()->create([
 *     'distance' => 42.2,
 *     'height_difference' => 350.5,
 *     'start_time' => '08:00',
 *     'start_place' => 'Broparken, Umeå',
 *     'name' => 'Marathon Route',
 *     'description' => 'Full marathon route with moderate elevation'
 * ]);
 *
 * // Update route details
 * $event->routeDetail()->update([
 *     'distance' => 45.0,
 *     'height_difference' => 400.0
 * ]);
 *
 * // Access route details from an event
 * $distance = $event->routeDetail->distance;
 * $heightDifference = $event->routeDetail->height_difference;
 * $startTime = $event->routeDetail->start_time;
 * $startPlace = $event->routeDetail->start_place;
 *
 * // Find events with specific route criteria
 * $longRouteEvents = Event::whereHas('routeDetail', function($query) {
 *     $query->where('distance', '>', 40);
 * })->get();
 */
class RouteDetail extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'event_uid',
        'distance',
        'height_difference',
        'start_time',
        'start_place',
        'name',
        'description',
        'track_link'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'distance' => 'float',
        'height_difference' => 'float',
        'start_time' => 'string',
    ];

    /**
     * Get the event that this route detail belongs to.
     * This is a one-to-one relationship as each Event represents a single route.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'event_uid', 'event_uid');
    }
}

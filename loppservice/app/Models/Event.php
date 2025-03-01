<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Event extends Model
{
    use HasFactory;
    use HasUuids;

    public function event()
    {
        return $this->morphTo();
    }

    public function eventconfiguration()
    {
        return $this->morphOne('App\Models\EventConfiguration', 'eventconfiguration');
    }

    protected $with = ['eventconfiguration'];
    protected $primaryKey = 'event_uid';
    protected $dateFormat = 'Y-m-d';
    // protected $keyType = "string";
    protected $startlisturl;

    protected $fillable = [
        'county_id',
        'event_group_uid',
        'organizer_id'
    ];

    /**
     * Get the county that the event belongs to.
     */
    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }

    /**
     * Get the event group that the event belongs to (optional).
     */
    public function eventGroup(): BelongsTo
    {
        return $this->belongsTo(EventGroup::class, 'event_group_uid', 'eventgroup_uid');
    }

    /**
     * Get the organizer of this event.
     */
    public function organizer()
    {
        return $this->belongsTo(Organizer::class, 'organizer_id', 'id');
    }

    /**
     * Get the route details for this event.
     *
     * Each Event has exactly one RouteDetail that contains information about:
     * - distance (in kilometers)
     * - height_difference (in meters)
     * - start_time
     * - name (optional)
     * - description (optional)
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function routeDetail()
    {
        return $this->hasOne(RouteDetail::class, 'event_uid', 'event_uid');
    }
}

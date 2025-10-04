<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use App\Traits\AutoOrganizerId;

class Event extends Model
{
    use HasFactory, HasUuids, AutoOrganizerId;

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'event_uid';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    protected $fillable = [
        'event_uid',
        'title',
        'description',
        'startdate',
        'enddate',
        'completed',
        'organizer_id' // This will be automatically set by the trait
    ];

    protected $casts = [
        'startdate' => 'date',
        'enddate' => 'date',
        'completed' => 'boolean'
    ];

    /**
     * Get the columns that should receive a unique identifier.
     *
     * @return array
     */
    public function uniqueIds(): array
    {
        return ['event_uid'];
    }

    /**
     * Get the route key for the model.
     *
     * @return string
     */
    public function getRouteKeyName(): string
    {
        return 'event_uid';
    }

    /**
     * Get the event configuration for this event.
     */
    public function eventconfiguration()
    {
        return $this->morphOne(EventConfiguration::class, 'eventconfiguration');
    }

    /**
     * Get the registrations for this event.
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class, 'course_uid', 'event_uid');
    }

    // The organizer() relationship is automatically provided by AutoOrganizerId trait
    // The forCurrentOrganizer() and forOrganizer() scopes are also provided by the trait
}

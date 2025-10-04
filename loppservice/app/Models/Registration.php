<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'registrations';
    protected $primaryKey = 'registration_uid';
    protected $fillable = ['course_uid','additional_information','use_physical_brevet_card'];

    protected $casts = [
        'reservation_valid_until' => 'date',
        'use_physical_brevet_card' => 'boolean',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_uid', 'person_uid');
    }

    /**
     * Get the event that this registration belongs to.
     * Note: course_uid in registrations is actually the event_uid
     */
    public function event(): BelongsTo
    {
        return $this->belongsTo(Event::class, 'course_uid', 'event_uid');
    }

    //protected $with = ['person'];
}

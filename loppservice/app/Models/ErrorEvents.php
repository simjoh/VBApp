<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class ErrorEvents extends Model
{
    use HasFactory;
    use HasUuids;


    protected $primaryKey = 'errorevent_uid';
    public $incrementing = false;

    protected $fillable = [
        'errorevent_uid',
        'publishedevent_uid',
        'registration_uid',
        'type',
        'error_code',
        'error_message'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'errorevent_uid' => 'string',
        'publishedevent_uid' => 'string',
        'registration_uid' => 'string'
    ];

    /**
     * Get the errorevent_uid as a string.
     *
     * @return string
     */
    public function getErroreventUidAttribute($value)
    {
        return (string) $value;
    }

    /**
     * Get the publishedevent_uid as a string.
     *
     * @return string
     */
    public function getPublishedeventUidAttribute($value)
    {
        return (string) $value;
    }

    /**
     * Get the registration_uid as a string.
     *
     * @return string
     */
    public function getRegistrationUidAttribute($value)
    {
        return (string) $value;
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $array = parent::toArray();
        // Convert UUID objects to strings
        if (isset($array['errorevent_uid'])) {
            $array['errorevent_uid'] = (string) $array['errorevent_uid'];
        }
        if (isset($array['publishedevent_uid'])) {
            $array['publishedevent_uid'] = (string) $array['publishedevent_uid'];
        }
        return $array;
    }
}

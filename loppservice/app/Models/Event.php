<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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


    public function organizer()
    {
        return $this->belongsTo(Organizer::class);
    }


}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    public function event()
    {
        return $this->morphTo();
    }

    public function eventconfiguration()
    {
        return $this->morphOne('App\Models\EventConfiguration', 'eventconfiguration');
    }

    protected $fillable = ['event_uid'];

    protected $with = ['eventconfiguration'];
    protected $primaryKey = 'event_uid';
    protected $dateFormat = 'Y-m-d';

}

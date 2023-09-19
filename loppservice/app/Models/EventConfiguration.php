<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EventConfiguration extends Model
{
    use HasFactory;

    public function eventconfiguration()
    {
        return $this->morphTo();
    }

    public function startnumberconfig()
    {
        return $this->morphOne(StartNumberConfig::class, 'startnumberconfig');
    }

    protected $with = ['startnumberconfig'];

    protected $table = 'eventconfigurations';

    protected $dateFormat = 'Y-m-d H:i';
}
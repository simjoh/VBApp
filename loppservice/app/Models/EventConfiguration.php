<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

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

    public function reservationconfig(): HasOne
    {
        return $this->hasOne(Reservationconfig::class);
    }

    protected $with = ['startnumberconfig','reservationconfig'];

    protected $table = 'eventconfigurations';

    protected $dateFormat = 'Y-m-d H:i';
}

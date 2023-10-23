<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class EventGroup extends Model
{
    use HasFactory;

    public function events(): MorphMany
    {
        return $this->morphMany(Event::class, 'event_uid');
    }

    protected $dateFormat = 'Y-m-d';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reservationconfig extends Model
{
    use HasFactory;

    public function eventconfiguration(): BelongsTo
    {
        return $this->belongsTo(EventConfiguration::class);
    }


}

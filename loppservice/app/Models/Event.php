<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    // use HasFactory;
    use HasUuids;

    protected $table = 'event';
    protected $primaryKey = 'event_uid';
}

<?php

namespace App\Models;

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class PublishedEvents extends Model
{
    use HasFactory;
    use HasUuids;


    protected $primaryKey = 'publishedevent_uid';
    public $incrementing = false;


}

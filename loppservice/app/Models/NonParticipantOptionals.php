<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NonParticipantOptionals extends Model
{

    use HasFactory;
    use HasUuids;

    public function NonParticipantOptionals()
    {
        return $this->morphTo();
    }

    // column name of key
    protected $primaryKey = 'optional_uid';

    // type of key
    protected $keyType = 'string';

    // whether the key is automatically incremented or not
    public $incrementing = false;

}
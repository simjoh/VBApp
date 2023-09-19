<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;
    use HasUuids;

    public function club()
    {
        return $this->morphTo();
    }

    protected $primaryKey = 'club_uid';

    protected $keyType = 'string';

    // whether the key is automatically incremented or not
    public $incrementing = false;
}

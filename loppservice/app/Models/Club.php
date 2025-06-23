<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Club extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = [
        'club_uid',
        'name',
        'acp_code',
        'description',
        'official_club'
    ];

    protected $casts = [
        'official_club' => 'boolean'
    ];

    public function club()
    {
        return $this->morphTo();
    }

    protected $primaryKey = 'club_uid';

    protected $keyType = 'string';

    // whether the key is automatically incremented or not
    public $incrementing = false;
}

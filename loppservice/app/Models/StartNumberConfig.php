<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StartNumberConfig extends Model
{

    use HasFactory;

    public function startnumberconfig()
    {
        return $this->morphTo();
    }

    protected $table = 'startnumberconfigs';
    public $timestamps = false;
}

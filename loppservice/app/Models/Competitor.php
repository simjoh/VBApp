<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
// use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Competitor extends Model
{
    // use HasFactory;
    use HasUuids;

    protected $table = 'competitors';
    protected $primaryKey = 'competitor_uid';
}

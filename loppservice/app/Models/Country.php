<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Country extends Model
{

    use HasFactory;
    protected $fillable = ['country_name','countrycode', 'flag_url', 'country_id'];
    protected $table = 'countries';
    protected $primaryKey = 'country_id';
}

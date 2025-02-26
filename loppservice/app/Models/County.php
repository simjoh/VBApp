<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class County extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'countys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'county_code',
        'name',
    ];

    /**
     * Get the municipalities for the county.
     */
    public function municipalities(): HasMany
    {
        return $this->hasMany(Municipality::class);
    }

    /**
     * Get the events in this county.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Municipality extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'municipalities';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'municipality_code',
        'name',
        'county_id',
    ];

    /**
     * Get the county that owns the municipality.
     */
    public function county(): BelongsTo
    {
        return $this->belongsTo(County::class);
    }
}

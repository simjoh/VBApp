<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EventGroup extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'name',
        'description'
    ];

    /**
     * Get the events in this group.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    protected $dateFormat = 'Y-m-d';
}

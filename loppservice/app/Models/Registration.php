<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Registration extends Model
{
    use HasFactory;
    use HasUuids;

    protected $table = 'registrations';
    protected $primaryKey = 'registration_uid';
    protected $fillable = ['course_uid','additional_information'];

    protected $casts = [
        'reservation_valid_until' => 'date',
    ];

    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    //protected $with = ['person'];
}

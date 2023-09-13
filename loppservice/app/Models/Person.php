<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = ['firstname','surname','registration_uid'];

    public function registrations(): BelongsTo
    {
        return $this->belongsTo(Registration::class);
    }

    public function adress(): HasOne
    {
        return $this->hasOne(Adress::class);
    }

    public function contactinformation(): HasOne
    {
        return $this->hasOne(Contactinformation::class);
    }

    protected $dateFormat = 'Y-m-d';

    protected $table = 'person';
    protected $primaryKey = 'person_uid';

    protected $with = ['adress','contactinformation'];
}

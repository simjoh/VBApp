<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Person extends Model
{
    use HasFactory;
    use HasUuids;

    protected $fillable = ['firstname','surname','registration_uid'];

    public function registration(): HasMany
    {
        return $this->hasMany(Registration::class, 'person_uid');
    }

    public function adress(): HasOne
    {
        return $this->hasOne(Adress::class, 'person_person_uid', 'person_uid');
    }

    public function contactinformation(): HasOne
    {
        return $this->hasOne(Contactinformation::class, 'person_person_uid', 'person_uid');
    }



    protected $dateFormat = 'Y-m-d';

    protected $table = 'person';
    protected $primaryKey = 'person_uid';

    protected $with = ['adress','contactinformation', 'registration'];
}

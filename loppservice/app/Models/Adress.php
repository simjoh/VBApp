<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Adress extends Model
{
    use HasFactory;
    use HasUuids;


    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class, 'person_person_uid', 'person_uid');
    }

    protected $table = 'adress';
    protected $fillable = ['adress'];
    protected $primaryKey = 'adress_uid';
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class contactinformation extends Model
{
    use HasFactory;
    use HasUuids;


    public function person(): BelongsTo
    {
        return $this->belongsTo(Person::class);
    }

    protected $fillable = ['tel'];
    protected $primaryKey = 'contactinformation_uid';
}

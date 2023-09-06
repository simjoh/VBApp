<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;
    use HasUuids;




    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'registration_uid';
}

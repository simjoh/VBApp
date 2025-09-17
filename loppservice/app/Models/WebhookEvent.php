<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WebhookEvent extends Model
{
    use HasFactory;

    protected $primaryKey = 'event_id';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'event_id',
        'event_type',
        'processed_at'
    ];

    protected $casts = [
        'processed_at' => 'datetime'
    ];
}

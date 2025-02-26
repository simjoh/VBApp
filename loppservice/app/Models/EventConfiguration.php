<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class EventConfiguration extends Model
{
    use HasFactory;

    // Define the startnumberconfig relationship
    public function startnumberconfig()
    {
        return $this->morphOne(StartNumberConfig::class, 'startnumberconfig');
    }

    // Define the reservationconfig relationship
    public function reservationconfig()
    {
        return $this->hasOne(Reservationconfig::class);
    }

    // Define the products relationship using hasMany
//    public function products()
//    {
//
//        return $this->morphMany(Product::class, 'productable');
//    }

    public function products()
    {
        return $this->morphToMany(Product::class, 'productable');
    }

    protected $with = ['startnumberconfig', 'reservationconfig', 'products'];

    protected $table = 'eventconfigurations';
    protected $dateFormat = 'Y-m-d H:i';

    protected $fillable = [
        'max_registrations',
        'registration_opens',
        'registration_closes',
        'resarvation_on_event',
        'use_stripe_payment'
    ];
}

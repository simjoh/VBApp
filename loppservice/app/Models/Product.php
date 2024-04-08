<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    // Define the productable relationship
    public function productable()
    {
        return $this->morphTo();
    }

    public function productables()
    {
        return $this->morphToMany(Productable::class, 'productable');
    }

    protected $primaryKey = 'productID';

}

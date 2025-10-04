<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\AutoOrganizerId;

class Product extends Model
{
    use HasFactory, AutoOrganizerId;

    protected $primaryKey = 'productID';

    protected $fillable = [
        'productname',
        'description',
        'full_description',
        'active',
        'categoryID',
        'price',
        'currency',
        'price_id',
        'stripe_product_id',
        'stripe_sync_status',
        'last_stripe_sync',
        'stripe_metadata',
        'sync_to_stripe',
        'productable_type',
        'productable_id',
        'organizer_id'
    ];

    protected $casts = [
        'active' => 'boolean',
        'stripe_metadata' => 'array',
        'last_stripe_sync' => 'datetime',
        'sync_to_stripe' => 'boolean'
    ];


    // Define the productable relationship
    public function productable()
    {
        return $this->morphTo();
    }

    public function productables()
    {
        return $this->morphToMany(Productable::class, 'productable');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'categoryID');
    }


    // Check if product is synced with Stripe
    public function isStripeSynced(): bool
    {
        return $this->stripe_sync_status === 'synced' && !empty($this->stripe_product_id);
    }

    // Check if product has Stripe price
    public function hasStripePrice(): bool
    {
        return !empty($this->price_id) && $this->isStripeSynced();
    }
}

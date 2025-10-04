<?php

namespace App\Traits;

use App\Context\UserContext;

trait AutoOrganizerId
{
    /**
     * Boot the trait.
     * Automatically sets organizer_id when creating a new model instance.
     */
    protected static function bootAutoOrganizerId()
    {
        // Automatically set organizer_id when creating a new model
        static::creating(function ($model) {
            if (empty($model->organizer_id)) {
                $userContext = UserContext::getInstance();
                if ($userContext->getOrganizerId()) {
                    $model->organizer_id = $userContext->getOrganizerId();
                }
            }
        });
    }

    /**
     * Get the organizer that owns this model.
     * Override this method in your model if you need a different relationship.
     */
    public function organizer()
    {
        return $this->belongsTo(\App\Models\Organizer::class, 'organizer_id', 'id');
    }

    /**
     * Scope a query to only include models for the current organizer.
     */
    public function scopeForCurrentOrganizer($query)
    {
        $userContext = UserContext::getInstance();
        $organizerId = $userContext->getOrganizerId();

        if ($organizerId) {
            return $query->where('organizer_id', $organizerId);
        }

        return $query->whereNull('organizer_id');
    }

    /**
     * Scope a query to only include models for a specific organizer.
     */
    public function scopeForOrganizer($query, $organizerId)
    {
        return $query->where('organizer_id', $organizerId);
    }
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Organizer extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'organization_name',
        'organization_number',
        'description',
        'website',
        'logo_svg',
        'contact_person_name',
        'email',
        'phone',
        'address',
        'postal_code',
        'city',
        'active',
        'gdpr_consent',
        'gdpr_consent_given_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'gdpr_consent' => 'boolean',
        'gdpr_consent_given_at' => 'datetime',
    ];

    /**
     * Get the events organized by this organizer.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Record GDPR consent for the organizer.
     *
     * @return void
     */
    public function giveGdprConsent(): void
    {
        $this->gdpr_consent = true;
        $this->gdpr_consent_given_at = now();
        $this->save();
    }

    /**
     * Withdraw GDPR consent for the organizer.
     *
     * @return void
     */
    public function withdrawGdprConsent(): void
    {
        $this->gdpr_consent = false;
        $this->gdpr_consent_given_at = null;
        $this->save();
    }

    /**
     * Check if the organizer has given GDPR consent.
     *
     * @return bool
     */
    public function hasGdprConsent(): bool
    {
        return $this->gdpr_consent;
    }

    /**
     * Validate and set the logo SVG.
     *
     * @param string|null $value
     * @return void
     */
    public function setLogoSvgAttribute(?string $value): void
    {
        if ($value === null) {
            $this->attributes['logo_svg'] = null;
            return;
        }

        // Basic SVG validation
        if (!Str::startsWith(trim($value), '<svg') || !Str::endsWith(trim($value), '</svg>')) {
            throw new \InvalidArgumentException('Invalid SVG format');
        }

        // Remove any potentially harmful content
        $value = strip_tags($value, [
            'svg', 'path', 'rect', 'circle', 'ellipse', 'line', 'polyline',
            'polygon', 'g', 'text', 'tspan', 'defs', 'style'
        ]);

        $this->attributes['logo_svg'] = $value;
    }

    /**
     * Get the logo SVG with safety checks.
     *
     * @param string|null $value
     * @return string|null
     */
    public function getLogoSvgAttribute(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        // Ensure it's a valid SVG before returning
        if (!Str::startsWith(trim($value), '<svg') || !Str::endsWith(trim($value), '</svg>')) {
            return null;
        }

        return $value;
    }
}

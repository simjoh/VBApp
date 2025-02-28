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
        'description',
        'website',
        'logo_svg',
        'contact_person_name',
        'email',
        'active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean'
    ];

    /**
     * Get the events organized by this organizer.
     */
    public function events(): HasMany
    {
        return $this->hasMany(Event::class);
    }

    /**
     * Set the logo SVG attribute with validation
     *
     * @param string|null $value
     * @return void
     */
    public function setLogoSvgAttribute($value)
    {
        if ($value === null) {
            $this->attributes['logo_svg'] = null;
            return;
        }

        // Basic SVG validation - allow XML declaration before SVG tag
        $value = trim($value);
        if (!preg_match('/<\?xml.*?>.*?<svg|<svg/i', $value) || !Str::endsWith($value, '</svg>')) {
            throw new \InvalidArgumentException('Invalid SVG format');
        }

        // Basic XSS prevention - remove script tags and potentially harmful attributes
        $value = preg_replace('/<script\b[^>]*>(.*?)<\/script>/is', '', $value);
        $value = preg_replace('/on\w+="[^"]*"/', '', $value);

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

        // Ensure it's a valid SVG before returning - allow XML declaration
        if (!preg_match('/<\?xml.*?>.*?<svg|<svg/i', trim($value)) || !Str::endsWith(trim($value), '</svg>')) {
            return null;
        }

        return $value;
    }
}

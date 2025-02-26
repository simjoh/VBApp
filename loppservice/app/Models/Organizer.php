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

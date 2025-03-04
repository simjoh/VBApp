<?php

namespace App\common\Rest\DTO;

/**
 * Route Details Data Transfer Object
 * 
 * Represents route details for an event in the LoppService API
 */
class RouteDetailsDTO extends BaseDTO
{
    /**
     * Unique identifier for the route details
     * @var int|null
     */
    public ?int $id = null;
    
    /**
     * Name of the route
     * @var string|null
     */
    public ?string $name = null;
    
    /**
     * Distance of the route in kilometers
     * @var float|null
     */
    public ?float $distance = null;
    
    /**
     * Elevation of the route in meters
     * @var float|null
     */
    public ?float $elevation = null;
    
    /**
     * Type of route (e.g., "loop", "out-and-back", "point-to-point")
     * @var string|null
     */
    public ?string $route_type = null;
    
    /**
     * Type of surface (e.g., "road", "gravel", "mixed")
     * @var string|null
     */
    public ?string $surface_type = null;
    
    /**
     * GPX file content (base64 encoded)
     * @var string|null
     */
    public ?string $gpx_file = null;
    
    /**
     * Event UID that these route details belong to
     * @var string|null
     */
    public ?string $event_uid = null;
    
    /**
     * Created timestamp
     * @var string|null
     */
    public ?string $created_at = null;
    
    /**
     * Updated timestamp
     * @var string|null
     */
    public ?string $updated_at = null;
    
    /**
     * Get the elevation gain (alias for elevation for backward compatibility)
     * 
     * @return float|null
     */
    public function getElevationGain(): ?float
    {
        return $this->elevation;
    }
    
    /**
     * Set the elevation gain (alias for elevation for backward compatibility)
     * 
     * @param float $elevationGain
     * @return void
     */
    public function setElevationGain(float $elevationGain): void
    {
        $this->elevation = $elevationGain;
    }
    
    /**
     * Magic getter to support legacy property access
     * 
     * @param string $name
     * @return mixed
     */
    public function __get(string $name)
    {
        switch ($name) {
            case 'uid':
                return $this->id;
            case 'elevation_gain':
                return $this->elevation;
            case 'gpx_file_url':
                return $this->gpx_file;
            default:
                return null;
        }
    }
    
    /**
     * Magic setter to support legacy property access
     * 
     * @param string $name
     * @param mixed $value
     * @return void
     */
    public function __set(string $name, $value): void
    {
        switch ($name) {
            case 'uid':
                $this->id = $value;
                break;
            case 'elevation_gain':
                $this->elevation = $value;
                break;
            case 'gpx_file_url':
                $this->gpx_file = $value;
                break;
        }
    }
} 
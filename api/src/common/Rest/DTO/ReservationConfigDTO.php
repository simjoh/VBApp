<?php

namespace App\common\Rest\DTO;

/**
 * Reservation Configuration Data Transfer Object
 * 
 * Represents the reservation configuration for an event in the LoppService API
 */
class ReservationConfigDTO extends BaseDTO
{
    /**
     * Unique identifier for the reservation configuration
     * @var int|null
     */
    public ?int $id = null;
    
    /**
     * Duration of the reservation in minutes
     * @var int|null
     */
    public ?int $duration = null;
    
    /**
     * Type of entity this configuration belongs to
     * @var string|null
     */
    public ?string $reservationconfig_type = null;
    
    /**
     * ID of the entity this configuration belongs to
     * @var int|null
     */
    public ?int $reservationconfig_id = null;
    
    /**
     * Created at timestamp
     * @var string|null
     */
    public ?string $created_at = null;
    
    /**
     * Updated at timestamp
     * @var string|null
     */
    public ?string $updated_at = null;
} 
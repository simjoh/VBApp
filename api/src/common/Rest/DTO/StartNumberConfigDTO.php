<?php

namespace App\common\Rest\DTO;

/**
 * Start Number Configuration Data Transfer Object
 * 
 * Represents the start number configuration for an event in the LoppService API
 */
class StartNumberConfigDTO extends BaseDTO
{
    /**
     * Unique identifier for the start number configuration
     * @var int|null
     */
    public ?int $id = null;
    
    /**
     * Start number begins at
     * @var int|null
     */
    public ?int $begins_at = null;
    
    /**
     * Start number ends at
     * @var int|null
     */
    public ?int $ends_at = null;
    
    /**
     * Increment value for start numbers
     * @var int|null
     */
    public ?int $increments = null;
    
    /**
     * Type of entity this configuration belongs to
     * @var string|null
     */
    public ?string $startnumberconfig_type = null;
    
    /**
     * ID of the entity this configuration belongs to
     * @var int|null
     */
    public ?int $startnumberconfig_id = null;
}
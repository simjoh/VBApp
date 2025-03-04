<?php

namespace App\common\Rest\DTO;

/**
 * Event Configuration Data Transfer Object
 * 
 * Represents the configuration for an event in the LoppService API
 */
class EventConfigurationDTO extends BaseDTO
{
    /**
     * Unique identifier for the event configuration
     * @var int|null
     */
    public ?int $id = null;
    
    /**
     * Maximum number of registrations allowed
     * @var int|null
     */
    public ?int $max_registrations = null;
    
    /**
     * Date when registration opens
     * @var string|null
     */
    public ?string $registration_opens = null;
    
    /**
     * Date when registration closes
     * @var string|null
     */
    public ?string $registration_closes = null;
    
    /**
     * Whether reservations are allowed on the event
     * @var int
     */
    public int $resarvation_on_event = 0;
    
    /**
     * Type of event configuration
     * @var string|null
     */
    public ?string $eventconfiguration_type = null;
    
    /**
     * ID of the event this configuration belongs to
     * @var string|null
     */
    public ?string $eventconfiguration_id = null;
    
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
     * Whether to use Stripe for payments
     * @var int
     */
    public int $use_stripe_payment = 0;
    
    /**
     * Start number configuration
     * @var StartNumberConfigDTO|null
     */
    public ?StartNumberConfigDTO $startnumberconfig = null;
    
    /**
     * Reservation configuration
     * @var ReservationConfigDTO|null
     */
    public ?ReservationConfigDTO $reservationconfig = null;
    
    /**
     * Products associated with this event (array of product IDs)
     * @var int[]
     */
    public array $products = [];
    
    /**
     * Create an EventConfigurationDTO from an array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $dto = parent::fromArray($data);
        
        // Handle nested objects
        if (isset($data['startnumberconfig'])) {
            $dto->startnumberconfig = StartNumberConfigDTO::fromArray($data['startnumberconfig']);
        }
        
        if (isset($data['reservationconfig'])) {
            $dto->reservationconfig = ReservationConfigDTO::fromArray($data['reservationconfig']);
        }
        
        // Handle products array (array of integers)
        if (isset($data['products']) && is_array($data['products'])) {
            $dto->products = $data['products'];
        }
        
        return $dto;
    }
    
    /**
     * Convert to array, including nested objects
     * 
     * @return array
     */
    public function toArray(): array
    {
        $data = parent::toArray();
        
        // Convert nested objects to arrays
        if ($this->startnumberconfig !== null) {
            $data['startnumberconfig'] = $this->startnumberconfig->toArray();
        }
        
        if ($this->reservationconfig !== null) {
            $data['reservationconfig'] = $this->reservationconfig->toArray();
        }
        
        // Products array is already an array of integers, no conversion needed
        
        return $data;
    }
} 
<?php

namespace App\common\Rest\DTO;

/**
 * Event Data Transfer Object
 * 
 * Represents an event in the LoppService API
 */
class EventDTO extends BaseDTO
{
    /**
     * Unique identifier for the event
     * @var string|null
     */
    public ?string $event_uid = null;
    
    /**
     * Title of the event
     * @var string
     */
    public string $title;
    
    /**
     * Description of the event
     * @var string|null
     */
    public ?string $description = null;
    
    /**
     * Start date of the event (YYYY-MM-DD)
     * @var string
     */
    public string $startdate;
    
    /**
     * End date of the event (YYYY-MM-DD)
     * @var string|null
     */
    public ?string $enddate = null;
    
    /**
     * Whether the event is completed (0 or 1)
     * @var int
     */
    public int $completed = 0;
    
    /**
     * Event type (e.g., "BRM")
     * @var string|null
     */
    public ?string $event_type = null;
    
    /**
     * Organizer ID
     * @var int|null
     */
    public ?int $organizer_id = null;
    
    /**
     * County ID
     * @var int|null
     */
    public ?int $county_id = null;
    
    /**
     * Event group UID
     * @var string|null
     */
    public ?string $event_group_uid = null;
    
    /**
     * Event configuration
     * @var EventConfigurationDTO|null
     */
    public ?EventConfigurationDTO $eventconfiguration = null;
    
    /**
     * Route details
     * @var RouteDetailsDTO|null
     */
    public ?RouteDetailsDTO $route_detail = null;
    
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
     * API links
     * @var array|null
     */
    public ?array $links = null;
    
    /**
     * Related resources
     * @var array|null
     */
    public ?array $related = null;
    
    /**
     * Create a collection of EventDTO objects from an array of event data
     * 
     * @param array $eventsData Array of event data
     * @return EventDTO[] Array of EventDTO objects
     */
    public static function fromCollection(array $eventsData): array
    {
        $events = [];
        foreach ($eventsData as $eventData) {
            $events[] = self::fromArray($eventData);
        }
        return $events;
    }
    
    /**
     * Create an EventDTO from an array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $dto = parent::fromArray($data);
        
        // Handle nested objects
        if (isset($data['eventconfiguration']) && is_array($data['eventconfiguration'])) {
            $dto->eventconfiguration = EventConfigurationDTO::fromArray($data['eventconfiguration']);
        }
        
        if (isset($data['route_detail']) && is_array($data['route_detail'])) {
            $dto->route_detail = RouteDetailsDTO::fromArray($data['route_detail']);
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
        if ($this->eventconfiguration !== null) {
            $data['eventconfiguration'] = $this->eventconfiguration->toArray();
        }
        
        if ($this->route_detail !== null) {
            $data['route_detail'] = $this->route_detail->toArray();
        }
        
        return $data;
    }
    
    /**
     * Get the event name (alias for title for backward compatibility)
     * 
     * @return string
     */
    public function getName(): string
    {
        return $this->title;
    }
    
    /**
     * Set the event name (alias for title for backward compatibility)
     * 
     * @param string $name
     * @return void
     */
    public function setName(string $name): void
    {
        $this->title = $name;
    }
    
    /**
     * Get the event UID (alias for event_uid for backward compatibility)
     * 
     * @return string|null
     */
    public function getUid(): ?string
    {
        return $this->event_uid;
    }
    
    /**
     * Set the event UID (alias for event_uid for backward compatibility)
     * 
     * @param string $uid
     * @return void
     */
    public function setUid(string $uid): void
    {
        $this->event_uid = $uid;
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
                return $this->event_uid;
            case 'name':
                return $this->title;
            case 'start_date':
                return $this->startdate;
            case 'end_date':
                return $this->enddate;
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
                $this->event_uid = $value;
                break;
            case 'name':
                $this->title = $value;
                break;
            case 'start_date':
                $this->startdate = $value;
                break;
            case 'end_date':
                $this->enddate = $value;
                break;
        }
    }
} 
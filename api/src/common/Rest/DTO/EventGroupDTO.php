<?php

namespace App\common\Rest\DTO;

/**
 * Event Group Data Transfer Object
 * 
 * Represents an event group in the LoppService API
 */
class EventGroupDTO extends BaseDTO
{
    /**
     * Unique identifier for the event group
     * @var string|null
     */
    public ?string $uid = null;
    
    /**
     * Name of the event group
     * @var string
     */
    public string $name;
    
    /**
     * Description of the event group
     * @var string|null
     */
    public ?string $description = null;
    
    /**
     * Start date of the event group (YYYY-MM-DD)
     * @var string
     */
    public string $startdate;
    
    /**
     * End date of the event group (YYYY-MM-DD)
     * @var string
     */
    public string $enddate;
    
    /**
     * Whether the event group is active
     * @var bool
     */
    public bool $active = false;
    
    /**
     * Whether the event group is canceled
     * @var bool
     */
    public bool $canceled = false;
    
    /**
     * Whether the event group is completed
     * @var bool
     */
    public bool $completed = false;
    
    /**
     * Array of event UIDs associated with this group
     * @var array|null
     */
    public ?array $event_uids = null;
    
    /**
     * Array of events associated with this group
     * @var array|null
     */
    public ?array $events = null;
    
    /**
     * Create a collection of EventGroupDTO objects from an array of event group data
     * 
     * @param array $eventGroupsData Array of event group data
     * @return EventGroupDTO[] Array of EventGroupDTO objects
     */
    public static function fromCollection(array $eventGroupsData): array
    {
        $eventGroups = [];
        foreach ($eventGroupsData as $eventGroupData) {
            $eventGroups[] = self::fromArray($eventGroupData);
        }
        return $eventGroups;
    }
    
    /**
     * Create an EventGroupDTO from an array
     * 
     * @param array $data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $dto = parent::fromArray($data);
        
        // Handle events data if present
        if (isset($data['events']) && is_array($data['events'])) {
            $dto->events = $data['events'];
            
            // Extract event UIDs from events array if not already set
            if (!isset($data['event_uids']) || !is_array($data['event_uids'])) {
                $dto->event_uids = array_map(function($event) {
                    return $event['event_uid'] ?? null;
                }, $data['events']);
                
                // Filter out null values
                $dto->event_uids = array_filter($dto->event_uids);
            }
        }
        
        // Set default values for boolean fields if not present
        $dto->active = $data['active'] ?? false;
        $dto->canceled = $data['canceled'] ?? false;
        $dto->completed = $data['completed'] ?? false;
        
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
        
        // Remove events from the array if present (to avoid duplication)
        if (isset($data['events'])) {
            unset($data['events']);
        }
        
        return $data;
    }
} 
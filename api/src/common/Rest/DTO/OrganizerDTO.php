<?php

namespace App\common\Rest\DTO;

/**
 * Organizer Data Transfer Object
 * 
 * Represents an organizer in the LoppService API
 */
class OrganizerDTO extends BaseDTO
{
    /**
     * Unique identifier for the organizer
     * @var int|null
     */
    public ?int $id = null;
    
    /**
     * Name of the organization
     * @var string
     */
    public string $organization_name;
    
    /**
     * Description of the organizer
     * @var string|null
     */
    public ?string $description = null;
    
    /**
     * Website URL for the organizer
     * @var string|null
     */
    public ?string $website = null;
    
    /**
     * SVG logo for the organizer (raw SVG or base64 encoded)
     * @var string|null
     */
    public ?string $logo_svg = null;
    
    /**
     * Contact person name
     * @var string
     */
    public string $contact_person_name;
    
    /**
     * Contact email for the organizer
     * @var string
     */
    public string $email;
    
    /**
     * Whether the organizer is active
     * @var bool
     */
    public bool $active = true;
    
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
     * HATEOAS links
     * @var array|null
     */
    public ?array $links = null;
    
    /**
     * Create a collection of OrganizerDTO objects from an array of organizer data
     * 
     * @param array $organizersData Array of organizer data
     * @return OrganizerDTO[] Array of OrganizerDTO objects
     */
    public static function fromCollection(array $organizersData): array
    {
        $organizers = [];
        foreach ($organizersData as $organizerData) {
            $organizers[] = self::fromArray($organizerData);
        }
        return $organizers;
    }
} 
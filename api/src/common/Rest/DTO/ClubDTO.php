<?php

namespace App\common\Rest\DTO;

use JsonSerializable;

/**
 * Data Transfer Object for Club information
 * Used for communication with LoppService API
 */
class ClubDTO implements JsonSerializable
{
    public ?string $club_uid = null;
    public ?string $name = null;
    public ?string $acp_kod = null;
    public ?string $description = null;
    public ?bool $official_club = null;

    /**
     * Create ClubDTO from array data
     * 
     * @param array $data
     * @return ClubDTO
     */
    public static function fromArray(array $data): ClubDTO
    {
        $dto = new ClubDTO();
        $dto->club_uid = $data['club_uid'] ?? null;
        $dto->name = $data['name'] ?? null;
        $dto->acp_kod = $data['acp_code'] ?? null;
        $dto->description = $data['description'] ?? null;
        $dto->official_club = $data['official_club'] ?? false;
        
        return $dto;
    }

    /**
     * Create an array of ClubDTO objects from collection data
     * 
     * @param array $collection
     * @return ClubDTO[]
     */
    public static function fromCollection(array $collection): array
    {
        return array_map(function($item) {
            return self::fromArray($item);
        }, $collection);
    }

    /**
     * Convert to array for API requests
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'club_uid' => $this->club_uid,
            'name' => $this->name,
            'acp_code' => $this->acp_kod,
            'description' => $this->description,
            'official_club' => $this->official_club
        ];
    }

    /**
     * JSON serialization
     * 
     * @return array
     */
    public function jsonSerialize(): mixed
    {
        return $this->toArray();
    }
} 
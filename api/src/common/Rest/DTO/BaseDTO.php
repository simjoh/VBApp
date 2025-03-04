<?php

namespace App\common\Rest\DTO;

/**
 * Base Data Transfer Object class
 * 
 * Provides common functionality for all DTOs including JSON serialization/deserialization
 */
abstract class BaseDTO implements \JsonSerializable
{
    /**
     * Create a DTO from an array of data
     * 
     * @param array $data The data to populate the DTO with
     * @return static The populated DTO
     */
    public static function fromArray(array $data): self
    {
        $dto = new static();
        
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->{$key} = $value;
            }
        }
        
        return $dto;
    }
    
    /**
     * Create a DTO from a JSON string
     * 
     * @param string $json The JSON string to deserialize
     * @return static The populated DTO
     */
    public static function fromJson(string $json): self
    {
        $data = json_decode($json, true);
        return static::fromArray($data);
    }
    
    /**
     * Convert the DTO to an array
     * 
     * @return array The DTO as an array
     */
    public function toArray(): array
    {
        return get_object_vars($this);
    }
    
    /**
     * Convert the DTO to a JSON string
     * 
     * @return string The DTO as a JSON string
     */
    public function toJson(): string
    {
        return json_encode($this);
    }
    
    /**
     * Specify data which should be serialized to JSON
     * 
     * @return array Data to be serialized
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
} 
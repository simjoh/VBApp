<?php

namespace App\common\Rest\DTO;

/**
 * Product Data Transfer Object
 * 
 * Represents a product in the LoppService API
 */
class ProductDTO extends BaseDTO
{
    /**
     * Unique identifier for the product
     * @var int|null
     */
    public ?int $id = null;
    
    /**
     * Name of the product
     * @var string|null
     */
    public ?string $name = null;
    
    /**
     * Description of the product
     * @var string|null
     */
    public ?string $description = null;
    
    /**
     * Price of the product
     * @var float|null
     */
    public ?float $price = null;
    
    /**
     * VAT percentage
     * @var float|null
     */
    public ?float $vat = null;
    
    /**
     * Whether the product is active
     * @var bool|null
     */
    public ?bool $active = null;
    
    /**
     * Type of entity this product belongs to
     * @var string|null
     */
    public ?string $product_type = null;
    
    /**
     * ID of the entity this product belongs to
     * @var int|null
     */
    public ?int $product_id = null;
    
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
    
    /**
     * Convert array data to DTO
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        $dto = new static();
        
        foreach ($data as $key => $value) {
            if (property_exists($dto, $key)) {
                $dto->$key = $value;
            }
        }
        
        return $dto;
    }
} 
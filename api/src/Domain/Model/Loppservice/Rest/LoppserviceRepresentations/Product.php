<?php

namespace App\Domain\Model\Loppservice\Rest\LoppserviceRepresentations;

class Product
{
    public int $productID;
    public string $productname;
    public string $description;
    public ?string $full_description;
    public int $active;
    public int $categoryID;
    public ?float $price;
    public string $created_at;
    public string $updated_at;
    public string $price_id;
    public string $productable_type;
    public int $productable_id;

    public function __construct(array $data)
    {
        $this->productID = $data['productID'];
        $this->productname = $data['productname'];
        $this->description = $data['description'];
        $this->full_description = $data['full_description'];
        $this->active = $data['active'];
        $this->categoryID = $data['categoryID'];
        $this->price = $data['price'];
        $this->created_at = $data['created_at'];
        $this->updated_at = $data['updated_at'];
        $this->price_id = $data['price_id'];
        $this->productable_type = $data['productable_type'];
        $this->productable_id = $data['productable_id'];
    }
}
<?php

namespace App\Domain\Model\Cache;

class SvgCache
{

    private $id;
    private $organizer_id;
    private $svg_blob;
    private $created_at;
    private $updated_at;

    // PDO instance for database interaction
    private PDO $pdo;
    private string $tableName;

    // Constructor to initialize the object with properties
    public function __construct()
    {

    }

    // Setter and Getter methods for private properties

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setOrganizerId(int $organizer_id): void
    {
        $this->organizer_id = $organizer_id;
    }

    public function getOrganizerId(): int
    {
        return $this->organizer_id;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setSvgBlob(string $svg_blob): void
    {
        $this->svg_blob = $svg_blob;
    }

    public function getSvgBlob(): string
    {
        return $this->svg_blob;
    }

    public function setCreatedAt(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    public function setUpdatedAt(?string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

}
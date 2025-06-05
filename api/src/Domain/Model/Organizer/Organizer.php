<?php

declare(strict_types=1);

namespace App\Domain\Model\Organizer;

use DateTime;

class Organizer
{
    private int $id;
    private string $organizationName;
    private ?string $description;
    private ?string $website;
    private ?string $website_pay;
    private ?string $logoSvg;
    private string $contactPersonName;
    private string $email;
    private bool $active;
    private ?string $club_uid;
    private ?DateTime $createdAt;
    private ?DateTime $updatedAt;

    public function __construct(
        int $id,
        string $organizationName,
        string $contactPersonName,
        string $email,
        ?string $description = null,
        ?string $website = null,
        ?string $website_pay = null,
        ?string $logoSvg = null,
        bool $active = true,
        ?string $club_uid = null,
        ?DateTime $createdAt = null,
        ?DateTime $updatedAt = null
    ) {
        $this->id = $id;
        $this->organizationName = $organizationName;
        $this->description = $description;
        $this->website = $website;
        $this->website_pay = $website_pay;
        $this->logoSvg = $logoSvg;
        $this->contactPersonName = $contactPersonName;
        $this->email = $email;
        $this->active = $active;
        $this->club_uid = $club_uid;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            id: (int)$data['id'],
            organizationName: $data['organization_name'],
            contactPersonName: $data['contact_person_name'],
            email: $data['email'],
            description: $data['description'] ?? null,
            website: $data['website'] ?? null,
            website_pay: $data['website_pay'] ?? null,
            logoSvg: $data['logo_svg'] ?? null,
            active: (bool)$data['active'],
            club_uid: $data['club_uid'] ?? null,
            createdAt: $data['created_at'] ? new DateTime($data['created_at']) : null,
            updatedAt: $data['updated_at'] ? new DateTime($data['updated_at']) : null
        );
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getOrganizationName(): string
    {
        return $this->organizationName;
    }

    public function setOrganizationName(string $organizationName): void
    {
        $this->organizationName = $organizationName;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    public function getWebsite(): ?string
    {
        return $this->website;
    }

    public function setWebsite(?string $website): void
    {
        $this->website = $website;
    }

    public function getWebsitePay(): ?string
    {
        return $this->website_pay;
    }

    public function setWebsitePay(?string $website_pay): void
    {
        $this->website_pay = $website_pay;
    }

    public function getLogoSvg(): ?string
    {
        return $this->logoSvg;
    }

    public function setLogoSvg(?string $logoSvg): void
    {
        $this->logoSvg = $logoSvg;
    }

    public function getContactPersonName(): string
    {
        return $this->contactPersonName;
    }

    public function setContactPersonName(string $contactPersonName): void
    {
        $this->contactPersonName = $contactPersonName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    public function isActive(): bool
    {
        return $this->active;
    }

    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    public function getClubUid(): ?string
    {
        return $this->club_uid;
    }

    public function setClubUid(?string $club_uid): void
    {
        $this->club_uid = $club_uid;
    }

    public function getCreatedAt(): ?DateTime
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?DateTime
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?DateTime $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
} 
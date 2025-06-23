<?php

namespace App\Domain\Model\Organizer\Rest;

use JsonSerializable;

class OrganizerRepresentation implements JsonSerializable
{
    private int $id;
    private ?string $organization_name = null;
    private ?string $description = null;
    private ?string $website = null;
    private ?string $website_pay = null;
    private ?string $logo_svg = null;
    private ?string $contact_person_name = null;
    private ?string $email = null;
    private ?bool $active = null;
    private ?string $club_uid = null;
    private array $links = [];

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getOrganizationName(): ?string
    {
        return $this->organization_name;
    }

    public function setOrganizationName(?string $organization_name): void
    {
        $this->organization_name = $organization_name;
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
        return $this->logo_svg;
    }

    public function setLogoSvg(?string $logo_svg): void
    {
        $this->logo_svg = $logo_svg;
    }

    public function getContactPersonName(): ?string
    {
        return $this->contact_person_name;
    }

    public function setContactPersonName(?string $contact_person_name): void
    {
        $this->contact_person_name = $contact_person_name;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(?bool $active): void
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

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    public function addLink($link): void
    {
        $this->links[] = $link;
    }

    public function jsonSerialize(): mixed
    {
        return (object)get_object_vars($this);
    }
} 
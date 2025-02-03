<?php

namespace App\Domain\Model\Acp\Rest;

use JsonSerializable;

class AcpReportRepresentation implements JsonSerializable
{
    private string $reportUid;
    private string $trackUid;
    private int $organizerId;
    private bool $readyForApproval;
    private ?string $markedAsReadyForApprovalBy;
    private bool $approved;
    private ?string $approvedBy;
    private ?string $createdAt;
    private ?string $updatedAt;
    private bool $deliveredToAcp;
    private int $brmId;
    private array $links = [];

    public function __construct()
    {
    }

    public function getReportUid(): string
    {
        return $this->reportUid;
    }

    public function setReportUid(string $reportUid): void
    {
        $this->reportUid = $reportUid;
    }

    public function getLinks(): array
    {
        return $this->links;
    }

    public function setLinks(array $links): void
    {
        $this->links = $links;
    }

    public function getBrmId(): int
    {
        return $this->brmId;
    }

    public function setBrmId(int $brmId): void
    {
        $this->brmId = $brmId;
    }

    public function isDeliveredToAcp(): bool
    {
        return $this->deliveredToAcp;
    }

    public function setDeliveredToAcp(bool $deliveredToAcp): void
    {
        $this->deliveredToAcp = $deliveredToAcp;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getApprovedBy(): ?string
    {
        return $this->approvedBy;
    }

    public function setApprovedBy(?string $approvedBy): void
    {
        $this->approvedBy = $approvedBy;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function setApproved(bool $approved): void
    {
        $this->approved = $approved;
    }

    public function getMarkedAsReadyForApprovalBy(): ?string
    {
        return $this->markedAsReadyForApprovalBy;
    }

    public function setMarkedAsReadyForApprovalBy(?string $markedAsReadyForApprovalBy): void
    {
        $this->markedAsReadyForApprovalBy = $markedAsReadyForApprovalBy;
    }

    public function isReadyForApproval(): bool
    {
        return $this->readyForApproval;
    }

    public function setReadyForApproval(bool $readyForApproval): void
    {
        $this->readyForApproval = $readyForApproval;
    }

    public function getOrganizerId(): int
    {
        return $this->organizerId;
    }

    public function setOrganizerId(int $organizerId): void
    {
        $this->organizerId = $organizerId;
    }

    public function getTrackUid(): string
    {
        return $this->trackUid;
    }

    public function setTrackUid(string $trackUid): void
    {
        $this->trackUid = $trackUid;
    }

    public function jsonSerialize(): mixed
    {
        return (object)get_object_vars($this);
    }

}
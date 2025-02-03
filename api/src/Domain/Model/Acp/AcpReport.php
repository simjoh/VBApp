<?php

namespace App\Domain\Model\Acp;

class AcpReport
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


    public function __construct(
        string  $reportUid,
        string  $trackUid,
        int     $organizerId,
        bool    $readyForApproval = false,
        ?string $markedAsReadyForApprovalBy = null,
        bool    $approved = false,
        ?string $approvedBy = null,
        ?string $createdAt = null,
        ?string $updatedAt = null,
        bool    $deliveredToAcp = false,
        int     $brmId
    )

    {
        $this->reportUid = $reportUid;
        $this->trackUid = $trackUid;
        $this->organizerId = $organizerId;
        $this->readyForApproval = $readyForApproval;
        $this->markedAsReadyForApprovalBy = $markedAsReadyForApprovalBy;
        $this->approved = $approved;
        $this->approvedBy = $approvedBy;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deliveredToAcp = $deliveredToAcp;
        $this->brmId = $brmId;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['report_uid'],
            $data['track_uid'],
            $data['organizer_id'],
            (bool)$data['ready_for_approval'],
            $data['marked_as_ready_for_approval_by'] ?? null,
            (bool)$data['approved'],
            $data['approved_by'] ?? null,
            $data['created_at'] ?? null,
            $data['updated_at'] ?? null,
            (bool)$data['delivered_to_acp'],
            $data['brm_id']
        );
    }


    // Getters
    public function getReportUid(): string
    {
        return $this->reportUid;
    }

    public function getTrackUid(): string
    {
        return $this->trackUid;
    }

    public function getOrganizerId(): int
    {
        return $this->organizerId;
    }

    public function isReadyForApproval(): bool
    {
        return $this->readyForApproval;
    }

    public function getMarkedAsReadyForApprovalBy(): ?string
    {
        return $this->markedAsReadyForApprovalBy;
    }

    public function isApproved(): bool
    {
        return $this->approved;
    }

    public function getApprovedBy(): ?string
    {
        return $this->approvedBy;
    }

    public function getCreatedAt(): ?string
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?string
    {
        return $this->updatedAt;
    }

    public function isDeliveredToAcp(): bool
    {
        return $this->deliveredToAcp;
    }

    public function getBrmId(): int
    {
        return $this->brmId;
    }


    public function setReportUid(string $reportUid): void
    {
        $this->reportUid = $reportUid;
    }

    public function setBrmId(int $brmId): void
    {
        $this->brmId = $brmId;
    }

    public function setDeliveredToAcp(bool $deliveredToAcp): void
    {
        $this->deliveredToAcp = $deliveredToAcp;
    }

    public function setUpdatedAt(?string $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function setCreatedAt(?string $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function setApprovedBy(?string $approvedBy): void
    {
        $this->approvedBy = $approvedBy;
    }

    public function setApproved(bool $approved): void
    {
        $this->approved = $approved;
    }

    public function setMarkedAsReadyForApprovalBy(?string $markedAsReadyForApprovalBy): void
    {
        $this->markedAsReadyForApprovalBy = $markedAsReadyForApprovalBy;
    }

    public function setReadyForApproval(bool $readyForApproval): void
    {
        $this->readyForApproval = $readyForApproval;
    }

    public function setOrganizerId(int $organizerId): void
    {
        $this->organizerId = $organizerId;
    }

    public function setTrackUid(string $trackUid): void
    {
        $this->trackUid = $trackUid;
    }
}
<?php

namespace App\Domain\Model\Acp;

class AcpReportBuilder
{
    private string $reportUid;
    private string $trackUid;
    private int $organizerId;
    private bool $readyForApproval = false;
    private ?string $markedAsReadyForApprovalBy = null;
    private bool $approved = false;
    private ?string $approvedBy = null;
    private ?string $createdAt = null;
    private ?string $updatedAt = null;
    private bool $deliveredToAcp = false;
    private int $brmId;

    public function setReportUid(string $reportUid): self
    {
        $this->reportUid = $reportUid;
        return $this;
    }

    public function setTrackUid(string $trackUid): self
    {
        $this->trackUid = $trackUid;
        return $this;
    }

    public function setOrganizerId(int $organizerId): self
    {
        $this->organizerId = $organizerId;
        return $this;
    }

    public function setReadyForApproval(bool $readyForApproval): self
    {
        $this->readyForApproval = $readyForApproval;
        return $this;
    }

    public function setMarkedAsReadyForApprovalBy(?string $markedAsReadyForApprovalBy): self
    {
        $this->markedAsReadyForApprovalBy = $markedAsReadyForApprovalBy;
        return $this;
    }

    public function setApproved(bool $approved): self
    {
        $this->approved = $approved;
        return $this;
    }

    public function setApprovedBy(?string $approvedBy): self
    {
        $this->approvedBy = $approvedBy;
        return $this;
    }

    public function setCreatedAt(?string $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    public function setUpdatedAt(?string $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    public function setDeliveredToAcp(bool $deliveredToAcp): self
    {
        $this->deliveredToAcp = $deliveredToAcp;
        return $this;
    }

    public function setBrmId(int $brmId): self
    {
        $this->brmId = $brmId;
        return $this;
    }

    public function build(): AcpReport
    {
        return new AcpReport(
            $this->reportUid,
            $this->trackUid,
            $this->organizerId,
            $this->readyForApproval,
            $this->markedAsReadyForApprovalBy,
            $this->approved,
            $this->approvedBy,
            $this->createdAt,
            $this->updatedAt,
            $this->deliveredToAcp,
            $this->brmId
        );
    }
}

//// Usage example
//$report = (new AcpReportBuilder())
//    ->setReportUid('123e4567-e89b-12d3-a456-426614174000')
//    ->setTrackUid('abc12345-6789-def0-1234-56789abcdef0')
//    ->setOrganizerId(1001)
//    ->setReadyForApproval(true)
//    ->setMarkedAsReadyForApprovalBy('John Doe')
//    ->setApproved(false)
//    ->setCreatedAt(date('Y-m-d H:i:s'))
//    ->setUpdatedAt(date('Y-m-d H:i:s'))
//    ->setDeliveredToAcp(false)
//    ->setBrmId(2002)
//    ->build();



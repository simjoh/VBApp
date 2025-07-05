<?php

namespace App\Domain\Model\Partisipant\Rest;

class MoveParticipantResponseRepresentation
{
    private array $success = [];
    private array $failed = [];
    private array $skipped = [];

    public function getSuccess(): array
    {
        return $this->success;
    }

    public function setSuccess(array $success): void
    {
        $this->success = $success;
    }

    public function getFailed(): array
    {
        return $this->failed;
    }

    public function setFailed(array $failed): void
    {
        $this->failed = $failed;
    }

    public function getSkipped(): array
    {
        return $this->skipped;
    }

    public function setSkipped(array $skipped): void
    {
        $this->skipped = $skipped;
    }
} 
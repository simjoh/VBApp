<?php

namespace App\Domain\Model\Acp\Repository;

use App\common\CurrentUser;
use App\Domain\Model\Acp\AcpReport;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;

class AcpReportParamBuilder
{
    public static function buildCreateParams(AcpReport $acpReport): array
    {
        return [
            'report_uid' => Uuid::uuid4()->toString(),
            'track_uid' => $acpReport->getTrackUid(),
            'organizer_id' => $acpReport->getOrganizerId(),
            'ready_for_approval' => false,
            'marked_as_ready_for_approval_by' => null,
            'approved' => false,
            'approved_by' => null,
            'delivered_to_acp' => false,
            'created_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
            'updated_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
        ];
    }

    public static function buildUpdateParams(AcpReport $acpReport): array
    {
        return [
            'report_uid' => $acpReport->getReportUid(),
            'track_uid' => $acpReport->getTrackUid(),
            'organizer_id' => $acpReport->getOrganizerId(),
            'ready_for_approval' => $acpReport->isReadyForApproval(),
            'approved' => $acpReport->isApproved(),
            'approved_by' => $acpReport->isApproved() ? CurrentUser::getUser()->getId() : null,
            'delivered_to_acp' => $acpReport->isDeliveredToAcp(),
            'updated_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
        ];
    }

    public static function buildApproveParams(string $report_uid): array
    {
        return [
            'report_uid' => $report_uid,
            'approved' => true,
            'approved_by' => CurrentUser::getUser()->getId(),
            'updated_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
        ];
    }

    public static function buildReadyForApprovalParams(string $report_uid): array
    {
        return [
            'report_uid' => $report_uid,
            'ready_for_approval' => true,
            'marked_as_ready_for_approval_by' => CurrentUser::getUser()->getId(),
            'updated_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
        ];
    }

    public static function buildDeliveredToAcpParams(string $report_uid): array
    {
        return [
            'report_uid' => $report_uid,
            'delivered_to_acp' => true,
            'updated_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
        ];
    }
}
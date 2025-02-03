<?php

namespace App\Domain\Model\Acp\Repository;


use App\common\CurrentUser;
use App\Domain\Model\Acp\AcpReportParicipants;
use DateTimeImmutable;
use DateTimeZone;
use Ramsey\Uuid\Uuid;

class AcpReportParticipantParamBuilder
{
    public static function buildCreateParams(AcpReportParicipants $participant): array
    {
        return [
            'participant_uid' => Uuid::uuid4()->toString(),
            'report_uid' => $participant->getReportUid(),
            'medaille' => $participant->getMedaille(),
            'temps' => $participant->getTemps(),
            'sexe' => $participant->getSexe(),
            'nom' => $participant->getNom(),
            'prenom' => $participant->getPrenom(),
            'naissance' => $participant->getNaissance(),
            'codeclub' => $participant->getCodeclub(),
            'nomclub' => $participant->getNomclub(),
            'delivered' => $participant->isDelivered(),
            'created_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
            'updated_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
            'organizer_id' => $participant->getOrganizerId() ?? CurrentUser::getUser()->getOrganizerId(),
        ];
    }

    public static function buildUpdateParams(AcpReportParicipants $participant): array
    {
        return [
            'participant_uid' => $participant->getParticipantUid(),
            'report_uid' => $participant->getReportUid(),
            'medaille' => $participant->getMedaille(),
            'temps' => $participant->getTemps(),
            'sexe' => $participant->getSexe(),
            'nom' => $participant->getNom(),
            'prenom' => $participant->getPrenom(),
            'naissance' => $participant->getNaissance(),
            'codeclub' => $participant->getCodeclub(),
            'nomclub' => $participant->getNomclub(),
            'delivered' => $participant->isDelivered(),
            'updated_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
            'organizer_id' => CurrentUser::getUser()->getOrganizerId(),
        ];
    }

    public static function buildApproveParams(string $participant_uid): array
    {
        return [
            'participant_uid' => $participant_uid,
            'delivered' => true,
            'updated_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
        ];
    }

    public static function buildReadyForApprovalParams(string $participant_uid): array
    {
        return [
            'participant_uid' => $participant_uid,
            'delivered' => false,
            'updated_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
        ];
    }

    public static function buildDeliveredToAcpParams(string $participant_uid): array
    {
        return [
            'participant_uid' => $participant_uid,
            'delivered' => true,
            'updated_at' => (new DateTimeImmutable('now', new DateTimeZone('UTC')))->format('Y-m-d H:i:s'),
        ];
    }
}
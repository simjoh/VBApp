<?php

namespace App\Domain\Model\Acp;

use App\Domain\Model\Acp\Rest\AcpReportRepresentation;
use Psr\Container\ContainerInterface;

class AcpReportAssembler
{
    private $settings;

    public function __construct(ContainerInterface $c)
    {
        $this->settings = $c->get('settings');
    }


    public static function toRepresentation(AcpReport $acpReport): AcpReportRepresentation
    {
        $representation = new AcpReportRepresentation();
        $representation->setReportUid($acpReport->getReportUid());
        $representation->setTrackUid($acpReport->getTrackUid());
        $representation->setOrganizerId($acpReport->getOrganizerId());
        $representation->setReadyForApproval($acpReport->isReadyForApproval());
        $representation->setMarkedAsReadyForApprovalBy($acpReport->getMarkedAsReadyForApprovalBy());
        $representation->setApproved($acpReport->isApproved());
        $representation->setApprovedBy($acpReport->getApprovedBy());
        $representation->setCreatedAt($acpReport->getCreatedAt());
        $representation->setUpdatedAt($acpReport->getUpdatedAt());
        $representation->setDeliveredToAcp($acpReport->isDeliveredToAcp());
        $representation->setBrmId($acpReport->getBrmId());
        $representation->setLinks(self::createLinks($acpReport));

        return $representation;
    }

    public static function toDomain(AcpReportRepresentation $representation): AcpReport
    {
        return new AcpReport(
            $representation->getReportUid(),
            $representation->getTrackUid(),
            $representation->getOrganizerId(),
            $representation->isReadyForApproval(),
            $representation->getMarkedAsReadyForApprovalBy(),
            $representation->isApproved(),
            $representation->getApprovedBy(),
            $representation->getCreatedAt(),
            $representation->getUpdatedAt(),
            $representation->isDeliveredToAcp(),
            $representation->getBrmId()
        );
    }

    private static function createLinks(AcpReport $acpReport)
    {

        return array();
    }
}
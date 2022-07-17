<?php

namespace App\Domain\Model\Track\Rest;

use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Stats\TrackStatistics;
use App\Domain\Permission\PermissionRepository;

class TrackMetricAssembly
{

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissinrepository = $permissionRepository;
    }

    public function toRepresentation(TrackStatistics $trackStatistics, array $permissions , string $curruentUserUid): TrackMetricsRepresentation
    {

        if(empty($permissions)){
            $permissions = $this->getPermissions($curruentUserUid);
        }

        $ttrackmetrics =  new TrackMetricsRepresentation();
        $ttrackmetrics->setCountDnf($trackStatistics->getCountDnf());
        $ttrackmetrics->setCountDns($trackStatistics->getCountDns());
        $ttrackmetrics->setCountFinished($trackStatistics->getCountFinished());
        $ttrackmetrics->setCountParticipants($trackStatistics->getCountParticipants());

        return $ttrackmetrics;
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("TRACK",$user_uid);
    }

}
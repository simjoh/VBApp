<?php

namespace App\Domain\Model\Track\Rest;

use App\Domain\Model\Stats\TrackStatistics;
use App\Domain\Model\Track\Track;
use App\Domain\Permission\PermissionRepository;

class TrackInformationAssembly
{

    public function __construct(PermissionRepository $permissionRepository, TrackMetricAssembly $trackMetricAssembly)
    {
        $this->permissinrepository = $permissionRepository;
        $this->trackMetricAssembly = $trackMetricAssembly;
    }

    public function toRepresentation(TrackRepresentation $track, array $permissions , string $curruentUserUid, ?TrackStatistics $trackStatistics): TrackInformationRepresentation
    {
        $ttracinfo =  new TrackInformationRepresentation();
        if(empty($permissions)){
            $permissions = $this->getPermissions($curruentUserUid);
        }
        if($trackStatistics != null){
            $statsfortrackrepre = $this->trackMetricAssembly->toRepresentation($trackStatistics, $permissions, $curruentUserUid);
            $ttracinfo->setTrackMetricsRepresentation($statsfortrackrepre);
        } else {
            $ttracinfo->setTrackMetricsRepresentation(new TrackMetricsRepresentation());
        }

        $ttracinfo->setTrackRepresentation($track);
        return $ttracinfo;
    }



    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("TRACK",$user_uid);
    }

}
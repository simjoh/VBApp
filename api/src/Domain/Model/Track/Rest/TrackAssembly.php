<?php

namespace App\Domain\Model\Track\Rest;

use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Track\Track;
use App\Domain\Permission\PermissionRepository;

class TrackAssembly
{

    public function __construct(PermissionRepository $permissionRepository,CheckpointsService $checkpointService)
    {
        $this->permissinrepository = $permissionRepository;
        $this->checkpointService = $checkpointService;
    }

    public function toRepresentations(array $trackArray, string $currentUserUid , array $permissions): array
    {
        if(empty($permissions)){
            $permissions = $this->getPermissions($currentUserUid);
        }
        $trackarray = array();
        foreach ($trackArray as $x =>  $track) {
            array_push($trackarray, (object) $this->toRepresentation($track,$permissions, $currentUserUid));
        }
        return $trackarray;
    }


    public function toRepresentation(Track $track, array $permissions , string $curruentUserUid): TrackRepresentation
    {

        if(empty($permissions)){
            $permissions = $this->getPermissions($curruentUserUid);
        }


        $trackRepresentation =  new TrackRepresentation();
        $trackRepresentation->setTrackUid($track->getTrackUid());
        $trackRepresentation->setDescriptions($track->getDescription());
        $trackRepresentation->setLinktotrack($track->getLink());
        $trackRepresentation->setTitle($track->getTitle());
        $trackRepresentation->setHeightdifference($track->getHeightdifference());
        $trackRepresentation->setDistance($track->getDistance());
        $trackRepresentation->setEventUid($track->getEventUid());
        if(!empty($track->getCheckpoints())){
            $trackRepresentation->setCheckpoints($this->checkpointService->checkpointsFor($track->getCheckpoints(),$curruentUserUid));
        }
        return $trackRepresentation;
    }

    public function totrack(TrackRepresentation $trackrepresentation): Track
    {
        $track = new Track();
        $track->setDescription($trackrepresentation->getDescriptions());
        $track->setTitle($trackrepresentation->getTitle());
        $track->setLink($trackrepresentation->getLinktotrack());
        $track->setHeightdifference($trackrepresentation->getHeightdifference());
        $track->setDistance($trackrepresentation->getDistance());
        $track->setTrackUid($trackrepresentation->getTrackUid());
        $track->setEventUid($trackrepresentation->getEventUid());
        if($trackrepresentation->getCheckpoints() !== null){
            $checkpoints = $trackrepresentation->getCheckpoints();
            if(!empty($checkpoints)){
                $checkpoints_uid = [];
                foreach ($checkpoints as $chp => $checkpoint){
                    $checkpoints_uid[]  =  $checkpoint['checkpoint_uid'];
                }
                $track->setCheckpoints($checkpoints_uid);
            }
        }

        return $track;
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("TRACK",$user_uid);
    }

}
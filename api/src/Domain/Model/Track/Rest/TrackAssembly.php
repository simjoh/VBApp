<?php

namespace App\Domain\Model\Track\Rest;

use App\common\Rest\Link;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Track\Track;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class TrackAssembly
{

    public function __construct(PermissionRepository $permissionRepository,CheckpointsService $checkpointService, ParticipantRepository $participantRepository, ContainerInterface $c)
    {
        $this->permissinrepository = $permissionRepository;
        $this->checkpointService = $checkpointService;
        $this->participantRepository = $participantRepository;
        $this->settings = $c->get('settings');
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
        $trackRepresentation->setHeightdifference("");
        $trackRepresentation->setDistance($track->getDistance());
        $trackRepresentation->setEventUid($track->getEventUid());
        $trackRepresentation->setActive($track->isActive());
        if($track->getStartDateTime() !== null){
            $trackRepresentation->setStartDateTime($track->getStartDateTime());
        }

        if(!empty($track->getCheckpoints())){
            $trackRepresentation->setCheckpoints($this->checkpointService->checkpointsFor($track->getCheckpoints(),$curruentUserUid));
        }

        $linkArray = array();

        $participants = $this->participantRepository->participantsOnTrack($track->getTrackUid());
        if(count($participants) == 0 || $participants == null){
            array_push($linkArray, new Link("relation.track.delete", 'DELETE', $this->settings['path'] .'track/' . $track->getTrackUid()));
        }

        if(count($participants) > 0 & $participants != null & $track->isActive() == true){
            array_push($linkArray, new Link("relation.track.publisresults", 'PUT', $this->settings['path'] .'publishresults/track/' . $track->getTrackUid() . "?publish=true"));
        }

        if($track->isActive() == false &&  count($participants) > 0){
            array_push($linkArray, new Link("relation.track.undopublisresults", 'PUT', $this->settings['path'] .'publishresults/track/' . $track->getTrackUid(). "?publish=false"));
        }

        array_push($linkArray, new Link("relation.track.tracktrack", 'GET', $this->settings['path'] . 'track/track/' . $track->getTrackUid()));

        array_push($linkArray, new Link("self", 'GET', $this->settings['path'] . 'track/' . $track->getTrackUid()));

        $trackRepresentation->setLinks($linkArray);

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
<?php

namespace App\Domain\Model\Track\Service;

use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Model\Track\Rest\TrackRepresentation;
use App\Domain\Model\Track\Track;
use Psr\Container\ContainerInterface;

class TrackService
{

    private $trackRepository;
    private $checkpointService;

    public function __construct(ContainerInterface $c , TrackRepository $trackRepository, CheckpointsService $checkpointService)
    {
        $this->trackRepository = $trackRepository;
       $this->checkpointService = $checkpointService;
    }

    public function allTracks(): array {
        $trackArray = $this->trackRepository->allTracks();
        // hämta checkpoints
       return $this->toRepresentations($trackArray);
    }

    public function getTrackByTrackUid(string $trackUid) : TrackRepresentation
    {
        $track = $this->trackRepository->getTrackByUid($trackUid);
        return $this->toRepresentation($track);
    }

    public function updateTrack(TrackRepresentation $trackrepresentation): ?TrackRepresentation
    {
        if(!empty($trackrepresentation)){
              $trackUpdated =    $this->trackRepository->updateTrack($this->totrack($trackrepresentation));
              return $this->toRepresentation($trackUpdated);
        }
        return null;

    }

    public function createTrack(TrackRepresentation $trackrepresentation): TrackRepresentation
    {
        $createdTrack = $this->trackRepository->createTrack($this->totrack($trackrepresentation));
        return $this->toRepresentation($createdTrack);
    }

    private function toRepresentations($trackArray): array
    {
        $trackarray = array();
        foreach ($trackArray as $x =>  $track) {
            array_push($trackarray, (object) $this->toRepresentation($track));
        }
        return $trackarray;
    }

    private function toRepresentation(Track $track): TrackRepresentation
    {
        $trackRepresentation =  new TrackRepresentation();
        $trackRepresentation->setTrackUid($track->getTrackUid());
        $trackRepresentation->setDescriptions($track->getDescription());
        $trackRepresentation->setLinktotrack($track->getLink());
        $trackRepresentation->setTitle($track->getTitle());
        $trackRepresentation->setHeightdifference($track->getHeightdifference());
        $trackRepresentation->setDistance($track->getDistance());
        $trackRepresentation->setEventUid($track->getEventUid());
        if(!empty($track->getCheckpoints())){
            $trackRepresentation->setCheckpoints($this->checkpointService->checkpointsFor($track->getCheckpoints()));
        }
        return $trackRepresentation;
    }

    private function totrack(TrackRepresentation $trackrepresentation): Track
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


}
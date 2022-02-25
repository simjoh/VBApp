<?php

namespace App\Domain\Model\Randonneur\Service;

use App\common\Exceptions\BrevetException;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Track\Repository\TrackRepository;

class RandonneurService
{


    public function __construct(CompetitorRepository $repository, ParticipantRepository $participantRepository, CheckpointsService $checkpointsService, TrackRepository $trackRepository)
    {
        $this->repository = $repository;
         $this->participantRepository = $participantRepository;
        $this->checkpointService = $checkpointsService;
        $this->trackrepository = $trackRepository;
    }

    public function checkpointsForRandonneur(?string $track_uid, $startnumber): ?array {

        $participant = $this->participantRepository->participantOntRackAndStartNumber($track_uid, $startnumber);
        if(!empty($participant)){
            // hämta checkpoints for track
            return $this->checkpointService->checkpointForTrack($participant->getTrackUid());
        }

        return null;
    }

    public function stampOnCheckpoint(?string $track_uid, $checkpoint_uid, string $startnumber ,string $current_useruid): bool{

        $track = $this->trackrepository->getTrackByUid($track_uid);

        if(!isset($track)){
            throw new BrevetException("Track not exists",5, null);
        }
        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);

        if(!isset($checkpoint)){
            throw new BrevetException("Checkpoint not exists",5, null);
        }

        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(),$startnumber);

        if(!isset($participant)){
            throw new BrevetException("Cannot find participant",5, null);
        }

       return  $this->participantRepository->stamp($participant->getParticipantUid(), $checkpoint->getCheckpointUid(), true);

    }

    public function markAsDnf(?string $track_uid, $checkpoint_uid, string $startnumber ,string $current_useruid): bool{

        $track = $this->trackrepository->getTrackByUid($track_uid);

        if(!isset($track)){
            throw new BrevetException("Track not exists",5, null);
        }
        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);

        if(!isset($checkpoint)){
            throw new BrevetException("Checkpoint not exists",5, null);
        }


        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(),$startnumber);

        if(!isset($participant)){
            throw new BrevetException("Cannot find participant",5, null);
        }

        return $this->participantRepository->setDnf($participant->getParticipantUid());

    }

    public function markAsDns(?string $track_uid, $checkpoint_uid, string $startnumber ,string $current_useruid){
        $track = $this->trackrepository->getTrackByUid($track_uid);

        if(!isset($track)){
            throw new BrevetException("Track not exists",5, null);
        }
        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);

        if(!isset($checkpoint)){
            throw new BrevetException("Checkpoint not exists",5, null);
        }


        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(),$startnumber);

        if(!isset($participant)){
            throw new BrevetException("Cannot find participant",5, null);
        }

        $this->participantRepository->setDns($participant->getParticipantUid());

    }

    public function rollbackStamp(?string $track_uid, $checkpoint_uid, string $startnumber ,string $current_useruid): bool{
        $track = $this->trackrepository->getTrackByUid($track_uid);

        if(!isset($track)){
            throw new BrevetException("Track not exists",5, null);
        }
        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);

        if(!isset($checkpoint)){
            throw new BrevetException("Checkpoint not exists",5, null);
        }

        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(),$startnumber);

        if(!isset($participant)){
            throw new BrevetException("Cannot find participant",5, null);
        }

        return $this->participantRepository->rollbackStamp($participant->getParticipantUid(), $checkpoint_uid);
    }
}
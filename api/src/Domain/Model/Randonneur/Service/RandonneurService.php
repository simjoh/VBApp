<?php

namespace App\Domain\Model\Randonneur\Service;

use App\common\Exceptions\BrevetException;
use App\common\Util;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Randonneur\Rest\RandonneurCheckpointAssembly;
use App\Domain\Model\Randonneur\Rest\TrackInfoRepresentation;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Model\Track\Rest\TrackAssembly;
use DateInterval;
use DateTime;

class RandonneurService
{


    public function __construct(CompetitorRepository $repository,
                                ParticipantRepository $participantRepository,
                                CheckpointsService $checkpointsService,
                                TrackRepository $trackRepository,
                                RandonneurCheckpointAssembly $randonneurCheckpointAssembly, TrackAssembly $trackAssembly)
    {
        $this->repository = $repository;
         $this->participantRepository = $participantRepository;
        $this->checkpointService = $checkpointsService;
        $this->trackrepository = $trackRepository;
        $this->randonneurCheckpointAssembly = $randonneurCheckpointAssembly;
        $this->trackAssembly = $trackAssembly;
    }

    public function checkpointsForRandonneur(?string $track_uid, $startnumber, string $current_user_uid): ?array {
        $checkpoints = [];
        $participant = $this->participantRepository->participantOntRackAndStartNumber($track_uid, $startnumber);
        $track = $this->trackrepository->getTrackByUid($track_uid);

        if(!empty($participant)){
            // hämta checkpoints for track
            $checkpoints = $this->checkpointService->checkpointForTrack($participant->getTrackUid());
           // return $checkpoints;

            $randonneurcheckpoints = [];
            foreach ($checkpoints as $checkpoint) {
                $stamped = $this->participantRepository->hasStampOnCheckpoint($participant->getParticipantUid(), $checkpoint->getCheckPointUId());
                $hasDnf = $this->participantRepository->hasDnf($participant->getParticipantUid());
                array_push($randonneurcheckpoints, $this->randonneurCheckpointAssembly->toRepresentation($checkpoint,$stamped,$track_uid, $current_user_uid, $startnumber, $hasDnf));
            }

            return $randonneurcheckpoints;
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

        if($participant->isDns()){
            throw new BrevetException("You have not started in a race ",5, null);
        }

//        // kolla att kontrollern har öppnat
//        if(date('Y-m-d H:i:s') < $checkpoint->getOpens()){
//            throw new BrevetException("Checkpoint opens:  " . date("Y-m-d H:i:s", strtotime($checkpoint->getOpens())) , 1, null);
//        }
//        // kolla att kontrollen har stängt
//        if(date('Y-m-d H:i:s') > $checkpoint->getClosing()){
//            throw new BrevetException("Checkpoint closes: " . date("Y-m-d H:i:s", strtotime($checkpoint->getClosing())) , 1, null);
//        }


        // kolla om start eller mål
        $isStart = $this->checkpointService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        if($isStart == true){
            if(date('Y-m-d H:i:s') < $track->getStartDateTime()){
                $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1);
            } else if(date('Y-m-d H:i:s') < $checkpoint->getClosing() && date('Y-m-d H:i:s') > $track->getStartDateTime()) {
                $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1);
            } else {
                throw new BrevetException("Error on checkin");
            }
            $participant->setStarted(1);
            $this->participantRepository->updateParticipant($participant);
            return true;
        }

        $isEnd = $this->checkpointService->isEndCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());
        if($isEnd == true){
            //om mål sätt måltid till tiden för instämpling och beräkna tiden mella första och sista instämpling. Sätt totaltiden i participant och markera finished
//            if(date('Y-m-d H:i:s') < $track->getStartDateTime()){
//                throw new BrevetException("Can not finish before the start of the race " . date("Y-m-d H:i:s", strtotime($track->getStartDateTime())), 1, null);
//            }
            $this->participantRepository->stampOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid);
            $participant->setDnf(false);
            $participant->setDns(false);
            $participant->setFinished(true);
            // beräkna tiden från första incheckning till nu och sätt tiden
            $participant->setTime(Util::secToHR(Util::calculateSecondsBetween($track->getStartDateTime())));
            $this->participantRepository->updateParticipant($participant);
            return true;
        }



        $this->participantRepository->stampOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid);
        return true;

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


    public function rollbackDnf(?string $track_uid, $checkpoint_uid, string $startnumber ,string $current_useruid): bool{

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

        return $this->participantRepository->rollbackDnf($participant->getParticipantUid());

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

        $isEnd = $this->checkpointService->isEndCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        if($isEnd == true){
            $this->participantRepository->rollbackStamp($participant->getParticipantUid(), $checkpoint_uid);
//            $participant->setDnf(false);
//            $participant->setDns(false);
            $participant->setFinished(false);
            $participant->setTime(null);
            $this->participantRepository->updateParticipant($participant);
            return true;
        }

        $isStart = $this->checkpointService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());
        if($isStart == true){
            $participant->setStarted(0);
            $this->participantRepository->updateParticipant($participant);
        }

        return $this->participantRepository->rollbackStamp($participant->getParticipantUid(), $checkpoint_uid);
    }

}
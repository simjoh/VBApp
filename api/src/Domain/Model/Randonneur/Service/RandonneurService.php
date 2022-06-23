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
use Psr\Container\ContainerInterface;

class RandonneurService
{


    public function __construct(CompetitorRepository $repository,
                                ParticipantRepository $participantRepository,
                                CheckpointsService $checkpointsService,
                                TrackRepository $trackRepository,
                                RandonneurCheckpointAssembly $randonneurCheckpointAssembly, TrackAssembly $trackAssembly, ContainerInterface $c )
    {
        $this->repository = $repository;
         $this->participantRepository = $participantRepository;
        $this->checkpointService = $checkpointsService;
        $this->trackrepository = $trackRepository;
        $this->randonneurCheckpointAssembly = $randonneurCheckpointAssembly;
        $this->trackAssembly = $trackAssembly;
        $this->settings = $c->get('settings');
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

        $today = date('Y-m-d');
        $startdate = date('Y-m-d', strtotime($track->getStartDateTime()));
        // Man ska bara kunna göra incheckning om det är samma da eller senare
        if($this->settings['demo'] == 'false') {
            if ($today < $startdate) {
                throw new BrevetException("You cannot checkin before startdate :  " . $startdate, 6, null);
            }
        }


        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(),$startnumber);


        if(!isset($participant)){
            throw new BrevetException("Cannot find participant",5, null);
        }

        $isStart = $this->checkpointService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        if($participant->isDns()){
            throw new BrevetException("You have not started in a race ",6, null);
        }
        if($this->settings['demo'] == 'false') {

            // är det start behöver vi inte göra kontroller då sätts starttiden till loppets starttid
            if($isStart == false){
                // kolla att kontrollern har öppnat
            if (date('Y-m-d H:i:s') < $checkpoint->getOpens()) {
                throw new BrevetException("Checkpoint not open. Opening date time:  " . date("Y-m-d H:i:s", strtotime($checkpoint->getOpens())), 6, null);
            }
            }
            // kolla att kontrollen har stängt
            if (date('Y-m-d H:i:s') > $checkpoint->getClosing()) {
                throw new BrevetException("Checkpoint is closed. Closing date time: " . date("Y-m-d H:i:s", strtotime($checkpoint->getClosing())), 6, null);
            }
        }

        // kolla om start eller mål


        if($isStart == true){

            if($this->settings['demo'] == 'false') {
                if ($today < $startdate) {
                    throw new BrevetException("You cannot checkin before startdate :  " . $startdate, 6, null);
                }
            }
            if(date('Y-m-d H:i:s') < $track->getStartDateTime()){
                $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1,0);
            } else if(date('Y-m-d H:i:s') < $checkpoint->getClosing() && date('Y-m-d H:i:s') > $track->getStartDateTime()) {
                $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, date('Y-m-d H:i:s'), 1,0);
            } else if(date('Y-m-d H:i:s') > $checkpoint->getClosing()) {
                if($this->settings['demo'] == 'false'){
                    if (date('Y-m-d H:i:s') > $checkpoint->getClosing()) {
                        throw new BrevetException("Checkpoint is closed. Closing date time: " . date("Y-m-d H:i:s", strtotime($checkpoint->getClosing())), 6, null);
                    }
                } else {
                    $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1, 0);
                }
            } else {
                throw new BrevetException("Error on checkin",  1, null);
            }
            $participant->setStarted(1);
            $this->participantRepository->updateParticipant($participant);
            return true;
        }

        $isEnd = $this->checkpointService->isEndCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());
        if($isEnd == true){

            if($participant->isDnf() == true){
                throw new BrevetException("You cannot finsish race if dnf is set", 6, null);
            }

            $countCheckpoints = $this->checkpointService->countCheckpointsForTrack($participant->getTrackUid());
            $oktofinish = $this->participantRepository->participantHasStampOnAllExceptFinish($track_uid,$checkpoint->getCheckpointUid(),$participant->getParticipantUid(), $countCheckpoints);

            if($oktofinish == false){
                throw new BrevetException("Cannot checkin on finish checkpoint due to missed checkins on one or more checkpoints. Contact race administrator", 6, null);
            }

            if($this->settings['demo'] == 'false') {
                if($track->getStartDateTime() != '-'){
                    // om mål sätt måltid till tiden för instämpling och beräkna tiden mella första och sista instämpling. Sätt totaltiden i participant och markera finished
                    if (date('Y-m-d H:i:s') < $track->getStartDateTime()) {
                        throw new BrevetException("Can not finish before the start of the race " . date("Y-m-d H:i:s", strtotime($track->getStartDateTime())), 1, null);
                    }
                }
            }

            $this->participantRepository->stampOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid,1, 0);
            $participant->setDnf(false);
            $participant->setDns(false);

            $participant->setFinished(true);
            // beräkna tiden från första incheckning till nu och sätt tiden
            $participant->setTime(Util::secToHR(Util::calculateSecondsBetween($track->getStartDateTime())));
            $this->participantRepository->updateParticipant($participant);
            return true;
        }

        if($participant->isStarted() == false){
            throw new BrevetException("You have to checkin on startcheckpoint before this", 6, null);
        }


        $this->participantRepository->stampOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid, 1, 0);
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

        $today = date('Y-m-d');
        $startdate = date('Y-m-d', strtotime($track->getStartDateTime()));

        if($this->settings['demo'] == 'false') {
            if ($today < $startdate) {
                throw new BrevetException("Checkin opens on startdate:  " . $startdate, 6, null);
            }
        }


        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(),$startnumber);

        if(!isset($participant)){
            throw new BrevetException("Cannot find participant",5, null);
        }

        if($participant->isStarted() == false){
            throw new BrevetException("You must start before you can break the race",6, null);
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


        $today = date('Y-m-d');
        $startdate = date('Y-m-d', strtotime($track->getStartDateTime()));

        if($this->settings['demo'] == 'false') {
            if ($today < $startdate) {
                throw new BrevetException("You cannot set DNS before startdate :  " . $startdate, 6, null);
            }
        }

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
            $participant->setStarted(false);
            $participant->setTime(null);
            $this->participantRepository->updateParticipant($participant);
        }

        return $this->participantRepository->rollbackStamp($participant->getParticipantUid(), $checkpoint_uid);
    }

}
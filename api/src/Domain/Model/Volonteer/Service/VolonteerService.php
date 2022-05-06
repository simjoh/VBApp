<?php

namespace App\Domain\Model\Volonteer\Service;


use App\common\Exceptions\BrevetException;
use App\common\Service\ServiceAbstract;
use App\common\Util;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\Volonteer\Repository\VolonteerRepository;
use App\Domain\Model\Volonteer\Rest\ParticipantToPassCheckpointAssembly;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class VolonteerService extends ServiceAbstract
{

    public function __construct(VolonteerRepository $volonteerRepository,
                                ParticipantToPassCheckpointAssembly
                                $participantToPassCheckpointAssembly,
                                PermissionRepository $permissionRepository,
                                UserRepository $userRepository,
                                ParticipantRepository $participantRepository,
                                CheckpointsService $checkpointsService, TrackRepository $trackRepository, ContainerInterface $c )
    {
        $this->volonteerRepository = $volonteerRepository;
        $this->participantToPassCheckpointAssembly = $participantToPassCheckpointAssembly;
        $this->permissionrepository = $permissionRepository;
        $this->userepository = $userRepository;
        $this->participantrepository = $participantRepository;
        $this->checkpointService = $checkpointsService;
        $this->trackrepository = $trackRepository;
        $this->settings = $c->get('settings');
    }

    public function getCheckpointsForTrack(string $track_uid,  string $currentUserUID): array{
        $permissions = $this->getPermissions($currentUserUID);



        return $this->checkpointService->checkpointForTrack($track_uid);


    }


    public function getRandoneursForCheckpoint(string $track_uid, string $checkpoint_uid, string $currentUserUID): array{
        $permissions = $this->getPermissions($currentUserUID);
        $pparticipantToPassCheckpoint = $this->volonteerRepository->getRandoneurToPassCheckpoint($track_uid, $checkpoint_uid);
        if(count($pparticipantToPassCheckpoint) > 0){
            return $this->participantToPassCheckpointAssembly->toRepresentations($pparticipantToPassCheckpoint,$currentUserUID,$permissions);
        }
        return array();
    }



    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("VOLONTEER",$user_uid);
        // TODO: Implement getPermissions() method.
    }

    public function rollbackRandonneurStamp(?string $track_uid, ?string $participant_uid, ?string $checkpoint_uid):bool
    {
        $track = $this->trackrepository->getTrackByUid($track_uid);

        if(!isset($track)){
            throw new BrevetException("Track not exists",5, null);
        }
        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);

        if(!isset($checkpoint)){
            throw new BrevetException("Checkpoint not exists",5, null);
        }

        $participant = $this->participantrepository->participantFor($participant_uid);

        if(!isset($participant)){
            throw new BrevetException("Cannot find participant",5, null);
        }

        $isEnd = $this->checkpointService->isEndCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        if($isEnd == true){
            $this->participantrepository->rollbackStamp($participant->getParticipantUid(), $checkpoint_uid);
//            $participant->setDnf(false);
//            $participant->setDns(false);
            $participant->setFinished(false);
            $participant->setTime(null);
            $this->participantrepository->updateParticipant($participant);
            return true;
        }

        $isStart = $this->checkpointService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());
        if($isStart == true){
            $participant->setStarted(0);
            $this->participantrepository->updateParticipant($participant);
        }

        $this->participantrepository->rollbackStamp($participant_uid, $checkpoint_uid);
        return true;
    }

    public function markRandonneurDnf(?string $track_uid, ?string $participant_uid, ?string $checkpoint_uid): bool
    {
        $track = $this->trackrepository->getTrackByUid($track_uid);

        if(!isset($track)){
            throw new BrevetException("Track not exists",5, null);
        }

        $participant = $this->participantrepository->participantFor($participant_uid);

        if(!isset($participant)){
            throw new BrevetException("Cannot find participant",5, null);
        }

       return  $this->participantrepository->setDnf($participant_uid, $checkpoint_uid);
    }

    public function rollbackRandonneurDnf(?string $track_uid, ?string $participant_uid, ?string $checkpoint_uid): bool
    {
        $track = $this->trackrepository->getTrackByUid($track_uid);

        if(!isset($track)){
            throw new BrevetException("Track not exists",5, null);
        }

        $participant = $this->participantrepository->participantFor($participant_uid);

        if(!isset($participant)){
            throw new BrevetException("Cannot find participant",5, null);
        }

        return  $this->participantrepository->rollbackDnf($participant_uid, $checkpoint_uid);
    }

    public function stampRandonneur(?string $track_uid, ?string $participant_uid, ?string $checkpoint_uid): bool
    {


        $track = $this->trackrepository->getTrackByUid($track_uid);

        if(!isset($track)){
            throw new BrevetException("Track not exists",5, null);
        }
        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);

        if(!isset($checkpoint)){
            throw new BrevetException("Checkpoint not exists",5, null);
        }

        $participant = $this->participantrepository->participantFor($participant_uid);

        if(!isset($participant)){
            throw new BrevetException("Cannot find participant",5, null);
        }


        if($this->settings['demo'] == 'false') {
            //  kolla att kontrollern har öppnat
            if (date('Y-m-d H:i:s') < $checkpoint->getOpens()) {
                throw new BrevetException("Checkpoint not open. Opening date time:  " . date("Y-m-d H:i:s", strtotime($checkpoint->getOpens())), 6, null);
            }
            // kolla att kontrollen har stängt
            if (date('Y-m-d H:i:s') > $checkpoint->getClosing()) {
                throw new BrevetException("Checkpoint is closed. Closing date time: " . date("Y-m-d H:i:s", strtotime($checkpoint->getClosing())), 6, null);
            }
        }

        // kolla om start eller mål
        $isStart = $this->checkpointService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        if($isStart == true){
            if(date('Y-m-d H:i:s') < $track->getStartDateTime()){
                $this->participantrepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1);
            } else if(date('Y-m-d H:i:s') < $checkpoint->getClosing() && date('Y-m-d H:i:s') > $track->getStartDateTime()) {
                $this->participantrepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1);
            } else {
                throw new BrevetException("Error on checkin", 1, null);
            }
            $participant->setStarted(1);
            $this->participantrepository->updateParticipant($participant);
            return true;
        }



        $isEnd = $this->checkpointService->isEndCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());
        if($isEnd == true){
            //om mål sätt måltid till tiden för instämpling och beräkna tiden mella första och sista instämpling. Sätt totaltiden i participant och markera finished
            if($this->settings['demo'] == 'false') {
                if($track->getStartDateTime() != '-') {
                    if (date('Y-m-d H:i:s') < $track->getStartDateTime()) {
                        throw new BrevetException("Can not finish before the start of the race " . date("Y-m-d H:i:s", strtotime($track->getStartDateTime())), 6, null);
                    }
                }
            }
            $this->participantrepository->stampOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid);
            $participant->setDnf(false);
            $participant->setDns(false);
            $participant->setFinished(true);
            // beräkna tiden från första incheckning till nu och sätt tiden
            $participant->setTime(Util::secToHR(Util::calculateSecondsBetween($track->getStartDateTime())));
            $this->participantrepository->updateParticipant($participant);
            return true;
        }

        if($participant->isStarted() == false){
            throw new BrevetException("You have to checkin on startcheckpoint before this", 6, null);
        }

        $this->participantrepository->stampOnCheckpoint($participant_uid, $checkpoint_uid);
        return true;
    }
}
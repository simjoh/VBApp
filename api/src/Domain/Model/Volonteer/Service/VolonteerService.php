<?php

namespace App\Domain\Model\Volonteer\Service;


use App\common\Exceptions\BrevetException;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\Volonteer\Repository\VolonteerRepository;
use App\Domain\Model\Volonteer\Rest\ParticipantToPassCheckpointAssembly;
use App\Domain\Permission\PermissionRepository;

class VolonteerService extends ServiceAbstract
{

    public function __construct(VolonteerRepository $volonteerRepository,
                                ParticipantToPassCheckpointAssembly
                                $participantToPassCheckpointAssembly,
                                PermissionRepository $permissionRepository,
                                UserRepository $userRepository,
                                ParticipantRepository $participantRepository,
                                CheckpointsService $checkpointsService, TrackRepository $trackRepository)
    {
        $this->volonteerRepository = $volonteerRepository;
        $this->participantToPassCheckpointAssembly = $participantToPassCheckpointAssembly;
        $this->permissionrepository = $permissionRepository;
        $this->userepository = $userRepository;
        $this->participantrepository = $participantRepository;
        $this->checkpointService = $checkpointsService;
        $this->trackrepository = $trackRepository;
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

    public function rollbackRandonneurStamp(?string $track_uid, ?string $participant_uid, ?string $checkpoint_uid)
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

        $this->participantrepository->rollbackStamp($participant_uid, $checkpoint_uid);
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

    public function stampRandonneur(?string $track_uid, ?string $participant_uid, ?string $checkpoint_uid)
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

        // kolla om start eller mål

        // om start sätt starttid till starttid om incheckning sker före annars aktuell tid

        //om mål sätt måltid till tiden för instämpling och beräkna tiden mella första och sista instämpling. Sätt totaltiden i participant och markera finished

        $this->participantrepository->stampOnCheckpoint($participant_uid, $checkpoint_uid);
    }
}
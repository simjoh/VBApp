<?php

namespace App\Domain\Model\Volonteer\Rest;


use App\common\Rest\Link;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\Volonteer\ParticipantToPassCheckpoint;
use App\Domain\Permission\PermissionRepository;


class ParticipantToPassCheckpointAssembly
{

    public function __construct(PermissionRepository $permissionRepository,CheckpointsService $checkpointService, UserRepository $userRepository)
    {
        $this->permissionrepository = $permissionRepository;
        $this->checkpointService = $checkpointService;
        $this->userrepository = $userRepository;
    }


    public function toRepresentations(array $participantToPassCheckpointArray, string $currentUserUid , array $permissions): array
    {
        if(empty($permissions)){
            $permissions = $this->getPermissions($currentUserUid);
        }
        $trackarrayReprs = array();
        foreach ($participantToPassCheckpointArray as $x =>  $participantToPassCheckpoint) {
            array_push($trackarrayReprs, (object) $this->toRepresentation($participantToPassCheckpoint,$permissions, $currentUserUid));
        }
        return $trackarrayReprs;
    }


    public function toRepresentation(ParticipantToPassCheckpoint $participantToPassCheckpoint, array $permissions , string $curruentUserUid): ParticipantToPassCheckpointRepresentation
    {

        if(empty($permissions)){
            $permissions = $this->getPermissions($curruentUserUid);
        }

        $participantToPassCheckpointRepresentation =  new ParticipantToPassCheckpointRepresentation();
        $participantToPassCheckpointRepresentation->setParticipantUid($participantToPassCheckpoint->getParticipantUid());
        $participantToPassCheckpointRepresentation->setTrackUid($participantToPassCheckpoint->getTrackUid());
        $participantToPassCheckpointRepresentation->setCheckpointUid($participantToPassCheckpoint->getCheckpointUid());
        $participantToPassCheckpointRepresentation->setAdress($participantToPassCheckpoint->getAdress());
        $participantToPassCheckpointRepresentation->setGivenName($participantToPassCheckpoint->getGivenName());
        $participantToPassCheckpointRepresentation->setFamilyName($participantToPassCheckpoint->getFamilyName());
        $participantToPassCheckpointRepresentation->setPassed($participantToPassCheckpoint->isPassed());
        $participantToPassCheckpointRepresentation->setStartNumber($participantToPassCheckpoint->getStartnumber());
        $participantToPassCheckpointRepresentation->setPassededDateTime($participantToPassCheckpoint->getPassededDateTime() == null ? "" : $participantToPassCheckpoint->getPassededDateTime());
        $participantToPassCheckpointRepresentation->setDnf($participantToPassCheckpoint->isDnf());
        $participantToPassCheckpointRepresentation->setStarted($participantToPassCheckpoint->isStarted());

        // bygg på med lite länkar
        $linkArray = array();
      //  foreach ($permissions as $x =>  $site) {
         //   if($site->hasWritePermission()){
                if(!$participantToPassCheckpoint->isPassed()){
                    array_push($linkArray, new Link("relation.volonteer.stamp", 'PUT', 'api/volonteer/track/' . $participantToPassCheckpoint->getTrackUid(). '/checkpoint/' . $participantToPassCheckpoint->getCheckpointUid(). '/randonneur/' . $participantToPassCheckpoint->getParticipantUid(). '/stamp'));
                } else {
                    array_push($linkArray, new Link("relation.volonteer.rollbackstamp", 'PUT', 'api/volonteer/track/' . $participantToPassCheckpoint->getTrackUid(). '/checkpoint/' . $participantToPassCheckpoint->getCheckpointUid(). '/randonneur/' . $participantToPassCheckpoint->getParticipantUid(). '/rollback'));
                }

        if(!$participantToPassCheckpoint->isDnf()){
            array_push($linkArray, new Link("relation.volonteer.setdnf", 'PUT', 'api/volonteer/track/' . $participantToPassCheckpoint->getTrackUid(). '/checkpoint/' . $participantToPassCheckpoint->getCheckpointUid(). '/randonneur/' . $participantToPassCheckpoint->getParticipantUid(). '/dnf'));
        } else {
            array_push($linkArray, new Link("relation.volonteer.rollbackdnf", 'PUT', 'api/volonteer/track/' . $participantToPassCheckpoint->getTrackUid(). '/checkpoint/' . $participantToPassCheckpoint->getCheckpointUid(). '/randonneur/' . $participantToPassCheckpoint->getParticipantUid(). '/rollbackdnf'));
        }

        array_push($linkArray, new Link("self", 'GET', 'api/participant/' . $participantToPassCheckpoint->getParticipantUid()));
               // break;
        //    }
//            if($site->hasReadPermission()){
//            };
     //   }
        $participantToPassCheckpointRepresentation->setLink($linkArray);
        return $participantToPassCheckpointRepresentation;
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("VOLONTEER",$user_uid);
    }


}
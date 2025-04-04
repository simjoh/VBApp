<?php

namespace App\Domain\Model\Volonteer\Rest;


use App\common\Rest\Link;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\Volonteer\ParticipantToPassCheckpoint;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;


class ParticipantToPassCheckpointAssembly
{

    private $permissionrepository;
    private $settings;
    private $checkpointService;
    private $userrepository;
    private $participantrepository;


    public function __construct(ContainerInterface $c ,PermissionRepository $permissionRepository,CheckpointsService $checkpointService, UserRepository $userRepository,ParticipantRepository $participantRepository)
    {
        $this->permissionrepository = $permissionRepository;
        $this->checkpointService = $checkpointService;
        $this->userrepository = $userRepository;
        $this->participantrepository = $participantRepository;
        $this->settings = $c->get('settings');
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
        $participantToPassCheckpointRepresentation->setVolonteerCheckin($participantToPassCheckpoint->isVolonteerCheckin());

        $hasCheckout = $this->participantrepository->hasCheckedOut($participantToPassCheckpoint->getParticipantUid(),  $participantToPassCheckpoint->getCheckPointUId());
        $participantToPassCheckpointRepresentation->setHasCheckouted($hasCheckout);
        // bygg på med lite länkar
        $linkArray = array();
      //  foreach ($permissions as $x =>  $site) {
         //   if($site->hasWritePermission()){
                if(!$participantToPassCheckpoint->isPassed()){
                    array_push($linkArray, new Link("relation.volonteer.stamp", 'PUT', $this->settings['path'] .'volonteer/track/' . $participantToPassCheckpoint->getTrackUid(). '/checkpoint/' . $participantToPassCheckpoint->getCheckpointUid(). '/randonneur/' . $participantToPassCheckpoint->getParticipantUid(). '/stamp'));
                } else {
                    array_push($linkArray, new Link("relation.volonteer.rollbackstamp", 'PUT', $this->settings['path'] . 'volonteer/track/' . $participantToPassCheckpoint->getTrackUid(). '/checkpoint/' . $participantToPassCheckpoint->getCheckpointUid(). '/randonneur/' . $participantToPassCheckpoint->getParticipantUid(). '/rollback'));
                }

        if (!$hasCheckout) {
            array_push($linkArray, new Link("relation.volonteer.checkout", 'PUT', $this->settings['path'] .'volonteer/' . $participantToPassCheckpoint->getParticipantUid() .'/track/'  . $participantToPassCheckpoint->getTrackUid(). "/startnumber/" . $participantToPassCheckpoint->getStartnumber() .  '/checkpoint/' . $participantToPassCheckpoint->getCheckpointUid().  '/checkoutfrom'));
        } else {
            array_push($linkArray, new Link("relation.volonteer.undocheckout", 'PUT', $this->settings['path'] . 'volonteer/' . $participantToPassCheckpoint->getParticipantUid() .'/track/' . $participantToPassCheckpoint->getTrackUid(). "/startnumber/" . $participantToPassCheckpoint->getStartnumber() .  '/checkpoint/' . $participantToPassCheckpoint->getCheckpointUid().  '/undocheckoutfrom'));
        }

        if(!$participantToPassCheckpoint->isDnf()){
            array_push($linkArray, new Link("relation.volonteer.setdnf", 'PUT', $this->settings['path'] . 'volonteer/track/' . $participantToPassCheckpoint->getTrackUid(). '/checkpoint/' . $participantToPassCheckpoint->getCheckpointUid(). '/randonneur/' . $participantToPassCheckpoint->getParticipantUid(). '/dnf'));
        } else {
            array_push($linkArray, new Link("relation.volonteer.rollbackdnf", 'PUT', $this->settings['path'] . 'volonteer/track/' . $participantToPassCheckpoint->getTrackUid(). '/checkpoint/' . $participantToPassCheckpoint->getCheckpointUid(). '/randonneur/' . $participantToPassCheckpoint->getParticipantUid(). '/rollbackdnf'));
        }

        array_push($linkArray, new Link("self", 'GET', $this->settings['path'] . 'participant/' . $participantToPassCheckpoint->getParticipantUid()));
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
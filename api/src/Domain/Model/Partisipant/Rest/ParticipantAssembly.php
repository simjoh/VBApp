<?php

namespace App\Domain\Model\Partisipant\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Event\Event;
use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Model\Partisipant\Participant;
use App\Domain\Permission\PermissionRepository;

class ParticipantAssembly
{

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissinrepository = $permissionRepository;
    }


    public function toRepresentations(array $eventsArray, string $currentUserUid): array {

        $permissions = $this->getPermissions($currentUserUid);
        $participants = array();
        foreach ($eventsArray as $x =>  $participant) {
            array_push($participants, (object) $this->toRepresentation($participant,$permissions));
        }
        return $participants;
    }


    public function toRepresentation(Participant $participant,  array $permissions): ParticipantRepresentation {


        $participantrepresentation = new ParticipantRepresentation();
        $participantrepresentation->setParticipantUid($participant->getParticipantUid());
        $participantrepresentation->setTrackUid($participant->getTrackUid());
        $participantrepresentation->setCompetitorUid($participant->getCompetitorUid());
        $participantrepresentation->setClubUid($participant->getClubUid());
        $participantrepresentation->setAcpcode($participant->getAcpkod() == null ? "" : $participant->getAcpkod());
        $participantrepresentation->setBrevenr($participant->getBrevenr() == null ? "" : $participant->getBrevenr());
        $participantrepresentation->setStartnumber($participant->getStartnumber());
        $participantrepresentation->setTime($participant->getTime());
        $participantrepresentation->setDns($participant->isDns());
        $participantrepresentation->setDnf($participant->isDnf());
        $participantrepresentation->setFinished($participant->isFinished());

        $linkArray = array();
        foreach ($permissions as $x =>  $site) {
            if($site->hasWritePermission()){
                array_push($linkArray, new Link("relation.participant.update", 'PUT', '/api/participant/' . $participant->getParticipantUid()));
                array_push($linkArray, new Link("relation.participant.delete", 'DELETE', '/api/participant/' . $participant->getParticipantUid));
                break;
            }
            if($site->hasReadPermission()){
                array_push($linkArray, new Link("self", 'GET', '/api/user/' . $participant->getParticipantUid()));
            };
        }

        $participantrepresentation->setLinks($linkArray);
        return $participantrepresentation;
    }



    public function toParticipation(ParticipantRepresentation $participantRepresentation): Participant {

        $participant = new Participant();
        $participant->setParticipantUid($participantRepresentation->getParticipantUid());


        return $participant;
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("PARTICIPANT",$user_uid);

    }

}
<?php

namespace App\Domain\Model\Partisipant\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Partisipant\Participant;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class ParticipantAssembly
{

    private $permissinrepository;
    private $settings;
    public function __construct(ContainerInterface $c, PermissionRepository $permissionRepository)
    {
        $this->permissinrepository = $permissionRepository;
        $this->settings = $c->get('settings');
    }


    public function toRepresentations(array $eventsArray, string $currentUserUid): array
    {

        $permissions = $this->getPermissions($currentUserUid);
        $participants = array();
        foreach ($eventsArray as $x => $participant) {
            array_push($participants, (object)$this->toRepresentation($participant, $permissions));
        }
        return $participants;
    }


    public function toRepresentation(Participant $participant, array $permissions): ?ParticipantRepresentation
    {

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
        $participantrepresentation->setStarted($participant->isStarted());
        $participantrepresentation->setBrevenr($participant->getBrevenr());
        $participantrepresentation->setFinished($participant->isFinished());
        $participantrepresentation->setDnsTimestamp($participant->getDnsTimestamp());
        $participantrepresentation->setDnfTimestamp($participant->getDnfTimestamp());

        $linkArray = array();
//        foreach ($permissions as $x =>  $site) {
//            if($site->hasWritePermission()){

        array_push($linkArray, new Link("relation.participant.checkpoints", 'GET', $this->settings['path'] . 'participant/' . $participant->getParticipantUid() . "/checkpointsforparticipant"));
        if ($participant->isStarted() != true) {
            array_push($linkArray, new Link("relation.participant.delete", 'DELETE', $this->settings['path'] . 'participant/' . $participant->getParticipantUid() . "/deleteParticipant"));
            if ($participant->isDns() != true) {
                array_push($linkArray, new Link("relation.participant.setdns", 'PUT', $this->settings['path'] . 'participant/' . $participant->getParticipantUid() . "/setdns"));
            } else {
                array_push($linkArray, new Link("relation.participant.rollbackdns", 'PUT', $this->settings['path'] . 'participant/' . $participant->getParticipantUid() . "/rollbackdns"));
            }
            array_push($linkArray, new Link("relation.participant.update", 'PUT', $this->settings['path'] . 'participant/' . $participant->getParticipantUid()));
        } else {
            if ($participant->isDnf() != true) {
                array_push($linkArray, new Link("relation.participant.setdnf", 'PUT', $this->settings['path'] . 'participant/' . $participant->getParticipantUid() . "/setdnf"));
            } else {
                array_push($linkArray, new Link("relation.participant.rollbackdnf", 'PUT', $this->settings['path'] . 'participant/' . $participant->getParticipantUid() . "/rollbackdnf"));
            }
        }

        if ($participant->isFinished() === true) {
            array_push($linkArray, new Link("relation.participant.updatetime", 'PUT', $this->settings['path'] . 'participant/' . $participant->getParticipantUid() . '/track/' . $participant->getTrackUid() . '/updateTime'));
            array_push($linkArray, new Link("relation.participant.addbrevenr", 'PUT', $this->settings['path'] . 'participant/' . $participant->getParticipantUid() . '/track/' . $participant->getTrackUid() . '/addbrevetnumber'));
        }
//                break;
//            }
//            if($site->hasReadPermission()){
        array_push($linkArray, new Link("self", 'GET', $this->settings['path'] . 'participant/' . $participant->getParticipantUid()));
//            };
//        }

        $participantrepresentation->setLinks($linkArray);
        return $participantrepresentation;
    }


    public function toParticipation(ParticipantRepresentation $participantRepresentation): Participant
    {

        $participant = new Participant();
        $participant->setParticipantUid($participantRepresentation->getParticipantUid());
        return $participant;
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("PARTICIPANT", $user_uid);

    }

}
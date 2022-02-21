<?php

namespace App\Domain\Model\Partisipant\Service;

use App\common\Exceptions\BrevetException;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Event\Service\EventService;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Partisipant\Rest\ParticipantAssembly;
use App\Domain\Model\Partisipant\Rest\ParticipantRepresentation;
use App\Domain\Model\Track\Repository\TrackRepository;
use Exception;
use Nette\Utils\Arrays;
use Psr\Container\ContainerInterface;

class ParticipantService
{

    public function __construct(ContainerInterface $c ,
                                TrackRepository $trackRepository, ParticipantRepository $participantRepository, ParticipantAssembly $participantAssembly, EventRepository $eventRepository)
    {
        $this->trackRepository = $trackRepository;
        $this->participantRepository = $participantRepository;
        $this->participantassembly = $participantAssembly;
        $this->eventrepository = $eventRepository;
    }

    public function participantsOnTrack(string $trackuid, string $currentUserUid): array {
        $participants = $this->participantRepository->participantsOnTrack($trackuid);

        if(!isset($participants)){
            return array();
        }
       return $this->participantassembly->toRepresentations($participants, $currentUserUid);
    }


    public function participantOnEvent(string $event_uid, string $currentUserUid): array {

        $track_uids = $this->eventrepository->tracksOnEvent($event_uid);

        if(count($track_uids) == 0){
            return array();
        }
        $participants = $this->participantRepository->getPArticipantsByTrackUids($track_uids);
        if(count($participants) == 0){
            return array();
        }
        return $this->participantassembly->toRepresentations($participants, $currentUserUid);
    }

    public function participantOnEventAndTrack(string $event_uid, $track_uid,  string $currentUserUid): array {

        $track_uids = $this->eventrepository->tracksOnEvent($event_uid);

        $tracks = [];
        foreach ($track_uids as $s => $ro){
            $tracks[] = $ro["track_uid"];
        }

        if (in_array($track_uid, $tracks)) {
            $participants = $this->participantRepository->participantsOnTrack($tracks[array_search($track_uid, $tracks)]);
        } else {
           throw new  BrevetException('Finns inget uid som matchar', 1, null);
        }
        if(count($participants) == 0){
            return array();
        }
        return $this->participantassembly->toRepresentations($participants, $currentUserUid);
    }

    public function allParticipants(string $currentUserUid): array {
        $participants = $this->participantRepository->allParticipants();
        if(!isset($participants)){
            return array();
        }
        return $this->participantassembly->toRepresentations($participants, $currentUserUid);
    }


    public function participantOnTrackAndClub(string $track_uid, string $club_uid, string $currentUserUid): array {
        $participants = $this->participantRepository->participantsbyTrackAndClub($track_uid, $club_uid);
        if(!isset($participants)){
            return array();
        }
        return $this->participantassembly->toRepresentations($participants, $currentUserUid);
    }

    public function participantsForCompetitor(string $competitor_uid, string $currentUserUid): array {
        $participantforcompetitor = $this->participantRepository->participantsForCompetitor($competitor_uid);
        if(!isset($participantforcompetitor)){
            return array();
        }
        return $this->participantassembly->toRepresentations($participantforcompetitor, $currentUserUid);
    }


    public function participantOnTrackAndStartNumber(string $track_uid, string $startnumber ,string $currentUserUid): array {
        $participant = $this->participantRepository->participantOntRackAndStartNumber($track_uid, $startnumber);
        if(!isset($participantforcompetitor)){
            return array();
        }
        return $this->participantassembly->toRepresentations($participant, $currentUserUid);
    }

    public function createparticipant(ParticipantRepresentation $participantRepresentation ,string $currentUserUid): ?ParticipantRepresentation {
        $participant = $this->participantRepository->createparticipant($this->participantassembly->toParticipation($participantRepresentation));
        if(!isset($participantforcompetitor)){
            return null;
        }
        return $this->participantassembly->toRepresentation($participant, $currentUserUid);
    }

    public function updatparticipant(ParticipantRepresentation $participantRepresentation ,string $currentUserUid): ?ParticipantRepresentation {
        $participant = $this->participantRepository->updateParticipant($this->participantassembly->toParticipation($participantRepresentation));
        if(!isset($participantforcompetitor)){
            return null;
        }
        return $this->participantassembly->toRepresentation($participant, $currentUserUid);
    }

    public function parseUplodesParticipant(){

    }


    public function updateBrevenr(string $brevenr, string $participant_uid , string $currentUserUid): ?ParticipantRepresentation {
        $updated = $this->participantRepository->updateBrevenr($brevenr, $participant_uid);

        if($updated == true){
            return $this->participantassembly->toRepresentation($this->participantRepository->participantFor($participant_uid), $currentUserUid);
        }
        if(!isset($participantforcompetitor)){
            return null;
        }

        return null;

    }













}
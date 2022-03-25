<?php

namespace App\Domain\Model\Partisipant\Service;

use App\common\Service\ServiceAbstract;
use App\Domain\Model\Club\Club;
use App\Domain\Model\Club\ClubRepository;
use App\Domain\Model\Competitor\Repository\CompetitorInfoRepository;
use App\Domain\Model\Competitor\Service\CompetitorService;
use App\common\Exceptions\BrevetException;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Partisipant\Participant;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Partisipant\Rest\ParticipantAssembly;
use App\Domain\Model\Partisipant\Rest\ParticipantRepresentation;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Permission\PermissionRepository;
use League\Csv\Reader;
use League\Csv\Statement;
use Nette\Utils\Strings;
use Psr\Container\ContainerInterface;

class ParticipantService extends ServiceAbstract
{

    public function __construct(ContainerInterface $c ,
                                TrackRepository $trackRepository,
                                ParticipantRepository $participantRepository,
                                ParticipantAssembly $participantAssembly,
                                EventRepository $eventRepository,
                                CompetitorService $competitorService,
                                CompetitorInfoRepository $competitorInfoRepository,
                                ClubRepository $clubRepository, PermissionRepository $permissionRepository)
    {
        $this->trackRepository = $trackRepository;
        $this->participantRepository = $participantRepository;
        $this->participantassembly = $participantAssembly;
        $this->eventrepository = $eventRepository;
        $this->settings = $c->get('settings');
        $this->competitorService = $competitorService;
        $this->competitorInfoRepository = $competitorInfoRepository;
        $this->clubrepository = $clubRepository;
        $this->permissionrepoitory = $permissionRepository;
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

    public function parseUplodesParticipant(string $filename, string $uploaddir, string $trackUid,string $currentUserUid): ?array {

        // vilken bana ska de registeraras på
        $track = $this->trackRepository->getTrackByUid($trackUid);

        if(!isset($track)){
            throw new BrevetException("Finns ingen bana med det uidet" . $trackUid, 1, null);
        }

        // Lös in filen som laddades upp
        $csv = Reader::createFromPath($this->settings['upload_directory']  . 'Deltagarlista-MSR-2022-test.csv', 'r');
        $csv->setDelimiter(";");
        $stmt = Statement::create();

        // Anropa denna sen
       // $stmt = $this->getCsv($filename);
        $records = $stmt->process($csv);

        $createdParticipants = [];
        foreach ($records as $record) {

            // se om det finns en sådan deltagare först
            $competitor = $this->competitorService->getCompetitorByNameAndBirthDate($record[2],$record[1], $record[12]);
            if(!isset($competitor)){
                // createOne
               $competitor =  $this->competitorService->createCompetitor($record[1],$record[2] , "", $record[12]);
                if(isset($competitor)){
                 //  $compInfo = $this->competitorInfoRepository->getCompetitorInfoByCompetitorUid($competitor->getId());
                   // if(!isset($compInfo)){
                        $this->competitorInfoRepository->creatCompetitorInfoForCompetitorParams($record[9], $record[10], $record[5], $record[6],$record[7], $record[8], $competitor->getId());
                   // }


                }
            }

            $existingParticipant = $this->participantRepository->participantForTrackAndCompetitor($trackUid, $competitor->getId());

            if(!isset($existingParticipant)){


            $participant = new Participant();
            $participant->setCompetitorUid($competitor->getId());
            $participant->setStartnumber($record[0]);
            $participant->setFinished(false);
            $participant->setTrackUid($track->getTrackUid());
            $participant->setDnf(false);
            $participant->setDns(false);
            $participant->setTime(null);
            $participant->setAcpkod("s");
            // kolla om klubben finns i databasen annars skapa vi en klubb
            $existingClub = $this->clubrepository->getClubByTitle($record[4]);

           if(!isset($existingClub)){
            $clubUid = $this->clubrepository->createClub("",  $record[4]);
            $participant->setClubUid($clubUid);
           } else {
               $participant->setClubUid($existingClub->getClubUid());
           }
            $participant->setTrackUid($trackUid);
            $participant->setRegisterDateTime($record[11]);

            $participantcreated = $this->participantRepository->createparticipant($participant);

                if(isset($participantcreated)){

                    $this->participantRepository->createTrackCheckpointsFor($participant,$this->trackRepository->checkpoints($trackUid));
                }
            $createdParticipants [] = $participant;
            }

            if(isset($participantcreated) && isset($competitor)){
                // skapa upp inloggning för cyklisten

                $this->competitorService->createCredentialFor($competitor->getId(), $participant->getParticipantUid(), $record[0], $record[13]);
            }

        }


      return $this->participantassembly->toRepresentations($createdParticipants, $currentUserUid);

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

    public function participantFor(?string $participantUid, string $user_uid): ?ParticipantRepresentation
    {
        $permissions = $this->getPermissions($user_uid);
        $participant = $this->participantRepository->participantFor($participantUid);

        if(!isset($participant)){
            return null;
        }

        return $this->participantassembly->toRepresentation($participant, $permissions);
    }


    private function getCsv(string $filename){
        $csv = Reader::createFromPath($this->settings['upload_directory']  . $filename, 'r');
        $csv->setDelimiter(";");
        return Statement::create();
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepoitory->getPermissionsTodata("PARTICIPANT",$user_uid);
    }
}
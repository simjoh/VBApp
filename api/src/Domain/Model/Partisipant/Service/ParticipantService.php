<?php

namespace App\Domain\Model\Partisipant\Service;

use App\common\Exceptions\BrevetException;
use App\common\Service\ServiceAbstract;
use App\common\Util;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Club\ClubRepository;
use App\Domain\Model\Club\Service\ClubService;
use App\Domain\Model\Competitor\Repository\CompetitorInfoRepository;
use App\Domain\Model\Competitor\Service\CompetitorInfoService;
use App\Domain\Model\Competitor\Service\CompetitorService;
use App\Domain\Model\Country\Repository\CountryRepository;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Loppservice\Rest\LoppservicePersonRepresentation;
use App\Domain\Model\Partisipant\Participant;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Partisipant\Rest\ParticipantAssembly;
use App\Domain\Model\Partisipant\Rest\ParticipantInformationAssembly;
use App\Domain\Model\Partisipant\Rest\ParticipantInformationRepresentation;
use App\Domain\Model\Partisipant\Rest\ParticipantRepresentation;
use App\Domain\Model\Randonneur\Service\RandonneurService;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Permission\PermissionRepository;
use Exception;
use League\Csv\Reader;
use League\Csv\Statement;
use Nette\Utils\DateTime;
use Psr\Container\ContainerInterface;

class ParticipantService extends ServiceAbstract
{

    private $trackRepository;
    private $participantRepository;
    private $participantassembly;
    private $eventrepository;
    private $competitorService;
    private $competitorInfoRepository;
    private $clubrepository;
    private $permissionrepoitory;
    private $competitorInformationAssembly;
    private $clubService;
    private $competitorInfoService;
    private $checkpointsService;
    private $randonneurservice;
    private $countryrepository;
    private $settings;

    public function __construct(ContainerInterface             $c,
                                TrackRepository                $trackRepository,
                                ParticipantRepository          $participantRepository,
                                ParticipantAssembly            $participantAssembly,
                                EventRepository                $eventRepository,
                                CompetitorService              $competitorService,
                                CompetitorInfoRepository       $competitorInfoRepository,
                                ClubRepository                 $clubRepository, PermissionRepository $permissionRepository,
                                ParticipantInformationAssembly $ciass,
                                ClubService                    $clubservice,
                                CompetitorInfoService          $competitorInfoService, CheckpointsService $checkpointsService, RandonneurService $randonneurService, CountryRepository $countryRepository)
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
        $this->competitorInformationAssembly = $ciass;
        $this->clubService = $clubservice;
        $this->competitorInfoService = $competitorInfoService;
        $this->checkpointsService = $checkpointsService;
        $this->randonneurservice = $randonneurService;
        $this->countryrepository = $countryRepository;
    }


    public function participantsOnTrackWithMoreInformation(string $trackuid, string $currentUserUid): array
    {

        $partisipants = $this->participantsOnTrack($trackuid, $currentUserUid);
        $participantsarray = array();
        foreach ($partisipants as $participant) {
            $competitor = $this->competitorService->getCompetitorByUid($participant->getCompetitorUid(), $currentUserUid);
            $competitor_info = $this->competitorInfoService->getCompetitorInfoByCompetitorUid($competitor->getCompetitorUid(), $currentUserUid);
            $club = $this->clubService->getClubByUid($participant->getClubUid(), $currentUserUid);
            array_push($participantsarray, $this->competitorInformationAssembly->toRepresentation($participant, $competitor, $club, $competitor_info, array()));
        }

        return $participantsarray;
    }

    public function participantsOnTrack(string $trackuid, string $currentUserUid): array
    {

        $track = $this->trackRepository->getTrackByUid($trackuid);

        $participants = $this->participantRepository->participantsOnTrack($trackuid);

        if (!isset($participants)) {
            return array();
        }
        return $this->participantassembly->toRepresentations($participants, $currentUserUid);
    }


    public function participantOnEvent(string $event_uid, string $currentUserUid): array
    {

        $track_uids = $this->eventrepository->tracksOnEvent($event_uid);

        if (count($track_uids) == 0) {
            return array();
        }
        $participants = $this->participantRepository->getPArticipantsByTrackUids($track_uids);
        if (count($participants) == 0) {
            return array();
        }
        return $this->participantassembly->toRepresentations($participants, $currentUserUid);
    }

    public function participantOnEventAndTrack(string $event_uid, $track_uid, string $currentUserUid): array
    {

        $track_uids = $this->eventrepository->tracksOnEvent($event_uid);

        $tracks = [];
        foreach ($track_uids as $s => $ro) {
            $tracks[] = $ro["track_uid"];
        }

        if (in_array($track_uid, $tracks)) {
            $participants = $this->participantRepository->participantsOnTrack($tracks[array_search($track_uid, $tracks)]);
        } else {
            throw new  BrevetException('Finns inget uid som matchar', 1, null);
        }
        if (count($participants) == 0) {
            return array();
        }
        return $this->participantassembly->toRepresentations($participants, $currentUserUid);
    }

    public function allParticipants(string $currentUserUid): array
    {
        $participants = $this->participantRepository->allParticipants();
        if (!isset($participants)) {
            return array();
        }
        return $this->participantassembly->toRepresentations($participants, $currentUserUid);
    }


    public function participantOnTrackAndClub(string $track_uid, string $club_uid, string $currentUserUid): array
    {
        $participants = $this->participantRepository->participantsbyTrackAndClub($track_uid, $club_uid);
        if (!isset($participants)) {
            return array();
        }
        return $this->participantassembly->toRepresentations($participants, $currentUserUid);
    }

    public function participantsForCompetitor(string $competitor_uid, string $currentUserUid): array
    {
        $participantforcompetitor = $this->participantRepository->participantsForCompetitor($competitor_uid);
        if (!isset($participantforcompetitor)) {
            return array();
        }
        return $this->participantassembly->toRepresentations($participantforcompetitor, $currentUserUid);
    }


    public function participantOnTrackAndStartNumber(string $track_uid, string $startnumber, string $currentUserUid): array
    {
        $participant = $this->participantRepository->participantOntRackAndStartNumber($track_uid, $startnumber);
        if (!isset($participantforcompetitor)) {
            return array();
        }
        return $this->participantassembly->toRepresentations($participant, $currentUserUid);
    }

    public function createparticipant(string $track_uid, ParticipantInformationRepresentation $participantInformationRepresentation, string $currentUserUid): ?ParticipantRepresentation
    {

        $track = $this->trackRepository->getTrackByUid($track_uid);

        if (!isset($track)) {
            throw new  BrevetException('Finns ingen bana med det uid:et', 1, null);
        }

        $club = $participantInformationRepresentation->getClubRepresentation();
        $competitorrepresentation = $participantInformationRepresentation->getCompetitorRepresentation();
        $competitorInfo = $participantInformationRepresentation->getCompetitorInforepresentation();
        $participantInput = $participantInformationRepresentation->getParticipant();


        if (isset($club) && $club->getClubUid() !== null) {
            $club = $this->clubrepository->getClubByUId($club->getClubUid());
        } else {
            $club_uid = $this->clubrepository->createClub($club->getAcpCode(), $club->getTitle());
            $club = $this->clubrepository->getClubByUId($club_uid);
        }

        if (isset($competitorrepresentation)) {
            if ($competitorrepresentation->getCompetitorUid() != null) {
                $competitorrepresentation = $this->competitorService->getCompetitorByUid($competitorrepresentation->getCompetitorUid(), "");
            } else {

                $competitor = $this->competitorService->createCompetitor($competitorrepresentation->getGivenName(), $competitorrepresentation->getFamilyName(), "ordernr", $competitorrepresentation->getBirthDate());

                if ($competitor->getCompetitorUid() != null) {
                    $this->competitorInfoRepository->creatCompetitorInfoForCompetitorParams($competitorInfo->getEmail(), $competitorInfo->getPhone(), $competitorInfo->getAdress(), $competitorInfo->getAdress(), $competitorInfo->getPlace(), $competitorInfo->getCountry(), $competitor->getCompetitorUid());
                }


            }
        } else {
            throw new  BrevetException('Kan inte skapa deltagare', 1, null);
        }

        $participant = new Participant();
        $participant->setCompetitorUid(isset($competitor) && $competitor->getCompetitorUid() != null ? $competitor->getCompetitorUid() : $competitorrepresentation->getCompetitorUid());
        $participant->setStartnumber($participantInput->getStartnumber());
        $participant->setFinished(false);
        $participant->setTrackUid($track->getTrackUid());
        $participant->setDnf(false);
        $participant->setDns(false);
        $participant->setTime(null);
        $participant->setStarted(false);
        $participant->setAcpkod("s");
        $participant->setClubUid($club->getClubUid());
        $participant->setTrackUid($track->getTrackUid());
        $participant->setRegisterDateTime(date('Y-m-d H:i:s'));


        $participantcreated = $this->participantRepository->createparticipant($participant);

        if (isset($participantcreated)) {

            $this->participantRepository->createTrackCheckpointsFor($participant, $this->trackRepository->checkpoints($track->getTrackUid()));
        }

        if (isset($participantcreated) && isset($competitor)) {
            // skapa upp inloggning för cyklisten
            $this->competitorService->createCredentialFor($competitor->getCompetitorUid(), $participant->getParticipantUid(), $participant->getStartnumber(), $participant->getStartnumber());
        }

        return $this->participantassembly->toRepresentation($participant, array());
    }

    public function updatparticipant(ParticipantRepresentation $participantRepresentation, string $currentUserUid): ?ParticipantRepresentation
    {
        $participant = $this->participantRepository->updateParticipant($this->participantassembly->toParticipation($participantRepresentation));
        if (!isset($participantforcompetitor)) {
            return null;
        }
        return $this->participantassembly->toRepresentation($participant, $currentUserUid);
    }

    public function parseUplodesParticipant(string $filename, string $uploaddir, string $trackUid, string $currentUserUid): ?array
    {

        // vilken bana ska de registeraras på
        $track = $this->trackRepository->getTrackByUid($trackUid);

        if (!isset($track)) {
            throw new BrevetException("Finns ingen bana med det uidet" . $trackUid, 1, null);
        }
        // Lös in filen som laddades upp
        //  $csv = Reader::createFromPath($this->settings['upload_directory']  . 'Deltagarlista-MSR-2022-test.csv', 'r');
        $csv = Reader::createFromPath($this->settings['upload_directory'] . $filename, 'r');
        $csv->setDelimiter(";");
        $stmt = Statement::create();

        // Anropa denna sen
        // $stmt = $this->getCsv($filename);
        $records = $stmt->process($csv);

        $createdParticipants = [];
        foreach ($records as $record) {

            // se om det finns en sådan deltagare först
            $competitor = $this->competitorService->getCompetitorByNameAndBirthDate($record[1], $record[2], $record[12]);

            if (!isset($competitor)) {
                // createOne
                $competitor = $this->competitorService->createCompetitor($record[1], $record[2], "", $record[12]);
                if (isset($competitor)) {
                    //  $compInfo = $this->competitorInfoRepository->getCompetitorInfoByCompetitorUid($competitor->getId());
                    // if(!isset($compInfo)){
                    $this->competitorInfoRepository->creatCompetitorInfoForCompetitorParams($record[9], $record[10], $record[5], $record[6], $record[7], $record[8], $competitor->getCompetitorUid());
                    // }
                }
            }
            $existingParticipant = $this->participantRepository->participantForTrackAndCompetitor($trackUid, $competitor->getCompetitorUid());


            if (!isset($existingParticipant)) {
                $participant = new Participant();

                $participant->setCompetitorUid($competitor->getCompetitorUid());

                $participant->setStartnumber($record[0]);
                $participant->setFinished(false);
                $participant->setTrackUid($track->getTrackUid());
                $participant->setDnf(false);
                $participant->setDns(false);
                $participant->setTime(null);
                $participant->setStarted(false);
                $participant->setAcpkod("s");

                // kolla om klubben finns i databasen annars skapa vi en klubb
                $existingClub = $this->clubrepository->getClubByTitle($record[4]);

                if (!isset($existingClub)) {
                    $clubUid = $this->clubrepository->createClub("", $record[4]);
                    $participant->setClubUid($clubUid);
                } else {
                    $participant->setClubUid($existingClub->getClubUid());
                }
                $participant->setTrackUid($trackUid);
                $participant->setRegisterDateTime($record[11]);

                $participantcreated = $this->participantRepository->createparticipant($participant);
                if (isset($participantcreated)) {

                    $this->participantRepository->createTrackCheckpointsFor($participant, $this->trackRepository->checkpoints($trackUid));
                }

                array_push($createdParticipants, $participant);
            }

            if (isset($participantcreated) && isset($competitor)) {
                // skapa upp inloggning för cyklisten
                $this->competitorService->createCredentialFor($competitor->getCompetitorUid(), $participant->getParticipantUid(), $record[0], $record[13]);
            }

        }

        return $this->participantassembly->toRepresentations($createdParticipants, $currentUserUid);

    }


    public function updateBrevenr(string $brevenr, string $participant_uid, string $currentUserUid): ?ParticipantRepresentation
    {
        $updated = $this->participantRepository->updateBrevenr($brevenr, $participant_uid);

        if ($updated == true) {
            return $this->participantassembly->toRepresentation($this->participantRepository->participantFor($participant_uid), $currentUserUid);
        }
        if (!isset($participantforcompetitor)) {
            return null;
        }

        return null;

    }

    public function participantFor(?string $participantUid, string $user_uid): ?ParticipantRepresentation
    {
        $permissions = $this->getPermissions($user_uid);
        $participant = $this->participantRepository->participantFor($participantUid);

        if (!isset($participant)) {
            return null;
        }

        return $this->participantassembly->toRepresentation($participant, $permissions);
    }


    public function deleteParticipant(?string $participant_uid, string $currentuserUid)
    {

        $participant = $this->participantRepository->participantFor($participant_uid);

        if ($participant == null) {
            throw new BrevetException("Deltagare finns inte", 5, null);
        }
        $track = $this->trackRepository->getTrackByUid($participant->getTrackUid());

        if ($track == null) {
            throw new BrevetException("Bana finns inte", 5, null);
        }


        $this->competitorService->deleteCompetitorCredentialForParticipant($participant->getParticipantUid(), $participant->getCompetitorUid());

        $this->participantRepository->deleteParticipantCheckpointOnTrackByParticipantUid($participant->getParticipantUid());

        $this->participantRepository->deleteParticipantByUID($participant->getParticipantUid());
    }

    /**
     * @throws BrevetException
     */
    public function deleteParticipantsOnTrack(?string $track_uid)
    {
        $track = $this->trackRepository->getTrackByUid($track_uid);

        if ($track == null) {
            throw new BrevetException("Bana finns inte", 5, null);
        }
        // kolla om loppet är använt har startats eller är passerat
        if (!$this->participantRepository->hasAnyoneStartedonTrack($track_uid)) {
            $participants = $this->participantRepository->getPArticipantsByTrackUids(array($track_uid));

            if (count($participants) > 0) {

                foreach ($participants as $participant) {

                    // tabort deltagarens credential för banan
                    $this->competitorService->deleteCompetitorCredentialForParticipant($participant->getParticipantUid(), $participant->getCompetitorUid());
                }
                // Tabort deltagarnas participant checkpoints
                $this->participantRepository->deleteParticipantCheckpointOnTrackByParticipantUid($participant->getParticipantUid());

                //Tabort själva deltagaren
                $rowsaffected = $this->participantRepository->deleteparticipantsOnTrack($track_uid);
            }

        } else {
            throw new BrevetException("Går inte att tabort deltagare. Deltagare har registrerade aktiviteter på banan", 5, null);
        }
    }

    public function checkpointsForParticipant(?string $participant_uid, string $currentuserUid): array
    {
        $participant = $this->participantRepository->participantFor($participant_uid);
        $track = $this->trackRepository->getTrackByUid($participant->getTrackUid());
        return $this->randonneurservice->getChecpointsForRandonneurForAdmin($participant, $track);
    }

    public function setDnf(?string $participant_uid)
    {
        $participant = $this->participantRepository->participantFor($participant_uid);
        $status = $this->participantRepository->setDnf($participant->getParticipantUid());

        if ($status == true) {
            $participant = $this->participantRepository->participantFor($participant_uid);
        }

    }

    public function rollbackDnf(?string $participant_uid, string $currentuserUid): bool
    {
        $participant = $this->participantRepository->participantFor($participant_uid);
        $status = $this->participantRepository->rollbackDnf($participant->getParticipantUid());
        return true;
    }


    public function setDns(?string $participant_uid, string $getAttribute)
    {
        $participant = $this->participantRepository->participantFor($participant_uid);
        $status = $this->participantRepository->setDns($participant->getParticipantUid());

        if ($status == true) {
            $participant = $this->participantRepository->participantFor($participant_uid);
        }
    }

    public function rollbackDns(?string $participant_uid, string $getAttribute)
    {
        $participant = $this->participantRepository->participantFor($participant_uid);
        $status = $this->participantRepository->rollbackDns($participant->getParticipantUid());
    }

    public function stampAdmin(?string $participant_uid, ?string $checkpoint_uid, string $getAttribute): array
    {

        $participant = $this->participantRepository->participantFor($participant_uid);
        $checkpoint = $this->checkpointsService->checkpointFor($checkpoint_uid);
        $track = $this->trackRepository->getTrackByUid($participant->getTrackUid());

        $isStart = $this->checkpointsService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());
        $isEnd = $this->checkpointsService->isEndCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        $today = date('Y-m-d');
        $startdate = date('Y-m-d', strtotime($track->getStartDateTime()));

        if ($isStart == true) {
            if ($this->settings['demo'] == 'false') {
                if ($today < $startdate) {
                    throw new BrevetException("You cannot checkin before startdate :  " . $startdate, 6, null);
                }
            }
            if (date('Y-m-d H:i:s') < $track->getStartDateTime()) {
                $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1, 0, null, null);
            } else if (date('Y-m-d H:i:s') < $checkpoint->getClosing() && date('Y-m-d H:i:s') > $track->getStartDateTime()) {
                $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1, 0, null, null);
            } else if (date('Y-m-d H:i:s') > $checkpoint->getClosing()) {
//                if($this->settings['demo'] == 'false'){
//                    if (date('Y-m-d H:i:s') > $checkpoint->getClosing()) {
//                        throw new BrevetException("Checkpoint is closed. Closing date time: " . date("Y-m-d H:i:s", strtotime($checkpoint->getClosing())), 6, null);
//                    }
//                } else {
                $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1, 0, null, null);
//                }
            } else {
                throw new BrevetException("Error on checkin", 1, null);
            }
            $participant->setStarted(1);
            $this->participantRepository->updateParticipant($participant);

            return $this->randonneurservice->getChecpointsForRandonneurForAdmin($participant, $track);


        }


        if ($isEnd == true) {
            if ($participant->isDnf() == true) {
                throw new BrevetException("You cannot finsish race if dnf is set", 6, null);
            }

            $countCheckpoints = $this->checkpointsService->countCheckpointsForTrack($participant->getTrackUid());
            $oktofinish = $this->participantRepository->participantHasStampOnAllExceptFinish($participant->getTrackUid(), $checkpoint->getCheckpointUid(), $participant->getParticipantUid(), $countCheckpoints);

//            if($oktofinish == false){
//                throw new BrevetException("Cannot checkin on finish checkpoint due to missed checkins on one or more checkpoints. Contact race administrator", 6, null);
//            }

//            if($this->settings['demo'] == 'false') {
            if ($track->getStartDateTime() != '-') {
                if ($this->settings['demo'] == 'false') {
                    // om mål sätt måltid till tiden för instämpling och beräkna tiden mella första och sista instämpling. Sätt totaltiden i participant och markera finished
                    if (date('Y-m-d H:i:s') < $track->getStartDateTime()) {
                        throw new BrevetException("Can not finish before the start of the race " . date("Y-m-d H:i:s", strtotime($track->getStartDateTime())), 1, null);
                    }
                }
            }
//            }

            $this->participantRepository->stampOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid, 1, 0, null, null);
            $participant->setDnf(false);
            $participant->setDns(false);

            $participant->setFinished(true);
            // beräkna tiden från första incheckning till nu och sätt tiden
            //  $participant->setTime(Util::secToHR(Util::calculateSecondsBetween($track->getStartDateTime())));
            $participant->setTime(Util::calculateSecondsBetween($track->getStartDateTime()));
            $this->participantRepository->updateParticipant($participant);
            return $this->randonneurservice->getChecpointsForRandonneurForAdmin($participant, $track);

        }

        if ($participant->isStarted() == false) {
            throw new BrevetException("You have to checkin on startcheckpoint before this", 6, null);
        }

        $this->participantRepository->stampOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid, 1, 0, null, null);
        return $this->randonneurservice->getChecpointsForRandonneurForAdmin($participant, $track);
    }

    public function rollbackstampAdmin(?string $participant_uid, ?string $checkpoint_uid, string $getAttribute)
    {

        $participant = $this->participantRepository->participantFor($participant_uid);

        $track = $this->trackRepository->getTrackByUid($participant->getTrackUid());

        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }
        $checkpoint = $this->checkpointsService->checkpointFor($checkpoint_uid);

        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }

        if (!isset($participant)) {
            throw new BrevetException("Cannot find participant", 5, null);
        }

        $isEnd = $this->checkpointsService->isEndCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        if ($isEnd == true) {
            $this->participantRepository->rollbackStamp($participant->getParticipantUid(), $checkpoint_uid);
//            $participant->setDnf(false);
//            $participant->setDns(false);
            $participant->setFinished(false);
            $participant->setTime(null);
            $this->participantRepository->updateParticipant($participant);
            return true;
        }

        $isStart = $this->checkpointsService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());
        if ($isStart == true) {
            $participant->setStarted(false);
            $participant->setTime(null);
            $this->participantRepository->updateParticipant($participant);
        }

        return $this->participantRepository->rollbackStamp($participant->getParticipantUid(), $checkpoint_uid);
    }

    public function addParticipantOnTrack(string $track_uid, ParticipantInformationRepresentation $participantInformationRepresentation)
    {

        $this->createparticipant($track_uid, $participantInformationRepresentation, "");
//        $track = $this->trackRepository->getTrackByUid($participant->getTrackUid());

    }

    public function addParticipantOnTrackFromLoppservice(LoppservicePersonRepresentation $loppservicePersonRepresentation, string $track_uid, $loppserviceRegistrationRepresentation, $club): bool
    {


        if ($track_uid == 'd32650ff-15f8-4df1-9845-d3dc252a7a84') {
            $track_uid = '0beda5d8-bbce-42d1-b7b6-4405a2e03db9';
        }

        try {
            $this->validateInput($loppserviceRegistrationRepresentation, $track_uid);
        } catch (BrevetException $brevetException) {
            throw $brevetException;
        }

        try {
            $registration = $loppserviceRegistrationRepresentation->registration;
            $participant_to_create = $loppserviceRegistrationRepresentation->participant;
            $contactinformation = $participant_to_create['contactinformation'];
            $adress = $participant_to_create['adress'];

            $track = $this->trackRepository->getTrackByUid($track_uid);
            if (!isset($track)) {
                throw new BrevetException("Track not exists", 5, null);
            }

            $registration_uid = $registration['registration_uid'];

            if (!isset($registration)) {
                throw new BrevetException("No registration found", 5, null);
            }

            $competitor = $this->competitorService->getCompetitorByUid3($participant_to_create['person_uid'], "");
            if (!isset($competitor)) {
                $competitorc = $this->competitorService->createCompetitorFromLoppservice($participant_to_create['firstname'], $participant_to_create['surname'], "", $participant_to_create['birthdate'], $participant_to_create['person_uid'],$participant_to_create['gender']);
                //  if ($competitorc) {
                $this->competitorInfoRepository->creatCompetitorInfoForCompetitorParamsFromLoppservice($contactinformation['email'], $contactinformation['tel'], $adress['adress'], $adress['postal_code'], $adress['city'], $this->countryrepository->countryFor($adress['country_id'])->country_name_sv, $participant_to_create['person_uid'], $adress['country_id']);
                // }
                $competitor = $competitorc;

            }
            if (isset($competitor)) {
                $participant = new Participant();
                $participant->setCompetitorUid($participant_to_create['person_uid']);
                $participant->setStartnumber($registration['startnumber']);
                $participant->setParticipantUid($registration_uid);
                $participant->setFinished(false);
                $participant->setTrackUid($track->getTrackUid());
                $participant->setDnf(false);
                $participant->setDns(false);
                $participant->setTime(null);
                $participant->setStarted(false);
                $participant->setAcpkod("s");

                if (isset($club)) {
                    // kolla om klubben finns i databasen annars skapa vi en klubb
                    $existingClub = $this->clubrepository->getClubByTitle($club['name']);
                    if (!isset($existingClub)) {
                        $clubUid = $this->clubrepository->createClub("", $club['name']);
                        $participant->setClubUid($clubUid);
                    } else {
                        $participant->setClubUid($existingClub->getClubUid());
                    }
                }

                $participant->setTrackUid($track->getTrackUid());
                $participant->setRegisterDateTime(new DateTime($registration['created_at']));
                $participantcreated = $this->participantRepository->createparticipant($participant);
                if (isset($participantcreated)) {


                    $this->participantRepository->createTrackCheckpointsFor($participant, $this->trackRepository->checkpoints($track->getTrackUid()));
                }
                if (isset($participantcreated) && isset($competitor)) {
                    // skapa upp inloggning för cyklisten
                    $this->competitorService->createCredentialFor($competitor->getCompetitorUid(), $participant->getParticipantUid(), $participant->getStartnumber(), $registration['ref_nr']);
                }
            }
            //   $this->competitorInfoRepository->creatCompetitorInfoForCompetitorParamsFromLoppservice($contactinformation['email'], $contactinformation['tel'], $adress['adress'], $adress['postal_code'], $adress['city'], $this->countryrepository->countryFor($adress['country_id'])->country_name_sv, $participant_to_create['person_uid'], $adress['country_id']);
            return true;
        } catch (Exception $e) {
            throw new BrevetException($e->getLine() . $e->getFile(), 5, null);
        }


    }

    private function validateInput($datatovalidate, $trackuid): bool
    {

        $participant = $this->participantRepository->participantFor($datatovalidate->registration['registration_uid']);
        if ($participant) {
            throw new BrevetException("An participant already exists withuid " . $datatovalidate->registration['registration_uid'], 5, null);
        }

        if (is_null($trackuid)) {
            throw new BrevetException("Track uid must be set", 5, null);
        }

        if (is_null($datatovalidate->registration['registration_uid'])) {
            return false;
        }

        if (is_null($datatovalidate->participant)) {
            throw new BrevetException("Participant must be set", 5, null);
        } else {

            if (!$datatovalidate->participant['adress']) {
                throw new BrevetException("Email must be provided", 5, null);
            }
        }

        if (is_null($datatovalidate->contactinformation)) {
            return false;
        }

        return true;
    }


    public function updateTime(?string $track_uid, ?string $participant_uid, string $newTime)
    {
        $this->participantRepository->updateTime($track_uid, $participant_uid, $newTime);

    }

    public function updateParticipantwithbrevenumber($participant_uid, $brevenr)
    {
        $participant = $this->participantRepository->participantFor($participant_uid);
        if ($participant === null) {
            throw new  BrevetException('Finns inget deltagare som matchar', 5, null);
        }
        $this->participantRepository->updateBrevenr($brevenr, $participant_uid);
    }


    private function getCsv(string $filename)
    {
        $csv = Reader::createFromPath($this->settings['upload_directory'] . $filename, 'r');
        $csv->setDelimiter(";");
        return Statement::create();
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepoitory->getPermissionsTodata("PARTICIPANT", $user_uid);
    }
}
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
use App\common\GlobalConfig;
use League\Csv\Writer;
use App\Domain\Model\Result\Repository\ResultRepository;
use App\Domain\Model\Organizer\Repository\OrganizerRepository;

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
    private $resultRepository;
    private $organizerRepository;

    public function __construct(
        ContainerInterface             $c,
        TrackRepository                $trackRepository,
        ParticipantRepository          $participantRepository,
        ParticipantAssembly            $participantAssembly,
        EventRepository                $eventRepository,
        CompetitorService              $competitorService,
        CompetitorInfoRepository       $competitorInfoRepository,
        ClubRepository                 $clubRepository,
        PermissionRepository $permissionRepository,
        ParticipantInformationAssembly $ciass,
        ClubService                    $clubservice,
        CompetitorInfoService          $competitorInfoService,
        CheckpointsService $checkpointsService,
        RandonneurService $randonneurService,
        CountryRepository $countryRepository,
        ResultRepository $resultRepository,
        OrganizerRepository $organizerRepository
    ) {
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
        $this->resultRepository = $resultRepository;
        $this->organizerRepository = $organizerRepository;
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
        return array($this->participantassembly->toRepresentation($participant, $this->getPermissions($currentUserUid)));
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

                if ($competitor->getId() != null) {
                    $this->competitorInfoRepository->creatCompetitorInfoForCompetitorParams($competitorInfo->getEmail(), $competitorInfo->getPhone(), $competitorInfo->getAdress(), $competitorInfo->getAdress(), $competitorInfo->getPlace(), $competitorInfo->getCountry(), $competitor->getId());
                }
            }
        } else {
            throw new  BrevetException('Kan inte skapa deltagare', 1, null);
        }

        $participant = new Participant();
        $participant->setCompetitorUid(isset($competitor) && $competitor->getId() != null ? $competitor->getId() : $competitorrepresentation->getCompetitorUid());
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
            $this->competitorService->createCredentialFor($competitor->getId(), $participant->getParticipantUid(), $participant->getStartnumber(), $participant->getStartnumber());
        }

        return $this->participantassembly->toRepresentation($participant, array());
    }

    public function updatparticipant(ParticipantRepresentation $participantRepresentation, string $currentUserUid): ?ParticipantRepresentation
    {
        $participant = $this->participantRepository->updateParticipant($this->participantassembly->toParticipation($participantRepresentation));
        if (!isset($participantforcompetitor)) {
            return null;
        }
        return $this->participantassembly->toRepresentation($participant, $this->getPermissions($currentUserUid));
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
                    $this->competitorInfoRepository->creatCompetitorInfoForCompetitorParams($record[9], $record[10], $record[5], $record[6], $record[7], $record[8], $competitor->getId());
                    // }
                }
            }
            $existingParticipant = $this->participantRepository->participantForTrackAndCompetitor($trackUid, $competitor->getId());


            if (!isset($existingParticipant)) {
                $participant = new Participant();

                $participant->setCompetitorUid($competitor->getId());

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
                $this->competitorService->createCredentialFor($competitor->getId(), $participant->getParticipantUid(), $record[0], $record[13]);
            }
        }

        return $this->participantassembly->toRepresentations($createdParticipants, $currentUserUid);
    }


    public function updateBrevenr(string $brevenr, string $participant_uid, string $currentUserUid): ?ParticipantRepresentation
    {
        $updated = $this->participantRepository->updateBrevenr($brevenr, $participant_uid);

        if ($updated == true) {
            return $this->participantassembly->toRepresentation($this->participantRepository->participantFor($participant_uid), $this->getPermissions($currentUserUid));
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

    public function deleteParticipantsOnTrack(?string $track_uid, mixed $currentuserUid)
    {
        $track = $this->trackRepository->getTrackByUid($track_uid);

        if ($track == null) {
            throw new BrevetException("Bana finns inte", 5, null);
        }
        // kolla om loppet är använt har startats eller är passerat
        if ($this->participantRepository->hasAnyoneStartedonTrack($track_uid) == false) {
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

    public function setDnf(?string $participant_uid, string $currentuserUid)
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

    public function updateCheckpointTime(?string $participant_uid, ?string $checkpoint_uid, string $stamptime, string $currentUserId): bool
    {
        $participant = $this->participantRepository->participantFor($participant_uid);

        if (!isset($participant)) {
            throw new BrevetException("Cannot find participant", 5, null);
        }

        $checkpoint = $this->checkpointsService->checkpointFor($checkpoint_uid);

        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }

        $track = $this->trackRepository->getTrackByUid($participant->getTrackUid());

        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }

        try {
            // Parse the incoming date, accommodating ISO-8601 format with 'Z' (UTC)
            $datetime = new \DateTime($stamptime);

            // Account for GMT+2 timezone if the input is in UTC (has 'Z' suffix)
            if (strpos($stamptime, 'Z') !== false) {
                // Set the timezone to GMT+2
                $datetime->setTimezone(new \DateTimeZone('Europe/Stockholm'));
            }

            // Format to MySQL datetime format (YYYY-MM-DD HH:MM:SS)
            $formattedDateTime = $datetime->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            throw new BrevetException("Invalid date format: " . $e->getMessage(), 5, $e);
        }

        // Check if this is a start or end checkpoint
        $isStart = $this->checkpointsService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());
        $isEnd = $this->checkpointsService->isEndCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        // Update the timestamp
        $this->participantRepository->stampOnCheckpointWithTime(
            $participant->getParticipantUid(),
            $checkpoint_uid,
            $formattedDateTime,
            $isStart ? 1 : 0,  // Set started flag if this is start checkpoint
            true,             // Mark as admin checkin
            null,             // No lat/lng for admin edits
            null
        );

        // Update participant state if needed
        if ($isStart) {
            $participant->setStarted(true);
            $this->participantRepository->updateParticipant($participant);
        } else if ($isEnd) {
            $participant->setFinished(true);
            $this->participantRepository->updateParticipant($participant);
        }

        return true;
    }

    public function updateCheckoutTime(?string $participant_uid, ?string $checkpoint_uid, string $checkouttime, string $currentUserId): bool
    {
        $participant = $this->participantRepository->participantFor($participant_uid);

        if (!isset($participant)) {
            throw new BrevetException("Cannot find participant", 5, null);
        }

        $checkpoint = $this->checkpointsService->checkpointFor($checkpoint_uid);

        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }

        $track = $this->trackRepository->getTrackByUid($participant->getTrackUid());

        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }

        try {
            // Parse the incoming date, accommodating ISO-8601 format with 'Z' (UTC)
            $datetime = new \DateTime($checkouttime);

            // Account for GMT+2 timezone if the input is in UTC (has 'Z' suffix)
            if (strpos($checkouttime, 'Z') !== false) {
                // Set the timezone to GMT+2
                $datetime->setTimezone(new \DateTimeZone('Europe/Stockholm'));
            }

            // Format to MySQL datetime format (YYYY-MM-DD HH:MM:SS)
            $formattedDateTime = $datetime->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            throw new BrevetException("Invalid date format: " . $e->getMessage(), 5, $e);
        }

        // Update the checkout timestamp in the database
        // This assumes there's a method to update checkout time - you might need to create one
        $this->participantRepository->updateCheckoutTime(
            $participant->getParticipantUid(),
            $checkpoint_uid,
            $formattedDateTime
        );

        return true;
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

                // For start checkpoint, set checkout time equal to checkin time
                $this->participantRepository->checkoutFromCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, 1, 0, $track->getStartDateTime());
            } else if (date('Y-m-d H:i:s') < $checkpoint->getClosing() && date('Y-m-d H:i:s') > $track->getStartDateTime()) {
                $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1, 0, null, null);

                // For start checkpoint, set checkout time equal to checkin time
                $this->participantRepository->checkoutFromCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, 1, 0, $track->getStartDateTime());
            } else if (date('Y-m-d H:i:s') > $checkpoint->getClosing()) {
                $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1, 0, null, null);

                // For start checkpoint, set checkout time equal to checkin time
                $this->participantRepository->checkoutFromCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, 1, 0, $track->getStartDateTime());
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

            if ($track->getStartDateTime() != '-') {
                if ($this->settings['demo'] == 'false') {
                    if (date('Y-m-d H:i:s') < $track->getStartDateTime()) {
                        throw new BrevetException("Can not finish before the start of the race " . date("Y-m-d H:i:s", strtotime($track->getStartDateTime())), 1, null);
                    }
                }
            }

            $this->participantRepository->stampOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid, 1, 0, null, null);

            // For end checkpoint, set checkout time equal to checkin time
            $this->participantRepository->checkoutFromCheckpoint($participant->getParticipantUid(), $checkpoint_uid, 1, 0);

            $participant->setDnf(false);
            $participant->setDns(false);

            $participant->setFinished(true);
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

    public function rollbackstampAdmin(?string $participant_uid, ?string $checkpoint_uid, string $getAttribute): bool
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

    public function addParticipantOnTrackFromLoppservice(LoppservicePersonRepresentation $loppservicePersonRepresentation, string $track_uid, $loppserviceRegistrationRepresentation, $club, $medal): bool
    {

        $finnsitabell = GlobalConfig::get($track_uid);

        echo "track_uid: " . $track_uid;

        if ($finnsitabell) {
            $track_uid = $finnsitabell;
        }

        if ($track_uid == 'ecc0fccc-ced8-493d-b671-e3379e2f5743') {
            $track_uid = '15689abe-ebdd-459a-8209-5b04815af486';
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
                $competitorc = $this->competitorService->createCompetitorFromLoppservice($participant_to_create['firstname'], $participant_to_create['surname'], "", $participant_to_create['birthdate'], $participant_to_create['person_uid'], $participant_to_create['gender']);
          /*       if ($competitorc) {
                    $this->competitorInfoRepository->creatCompetitorInfoForCompetitorParamsFromLoppservice($contactinformation['email'], $contactinformation['tel'], $adress['adress'], $adress['postal_code'], $adress['city'], $this->countryrepository->countryFor($adress['country_id'])->country_name_sv, $participant_to_create['person_uid'], $adress['country_id']);
                } */
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
                $participant->setMedal($medal);

                if (isset($club)) {
                    // kolla om klubben finns i databasen annars skapa vi en klubb
                    $existingClub = $this->clubrepository->getClubByUId($club['club_uid']);
                    if (!isset($existingClub)) {
                        $existingClub = $this->clubrepository->getClubByTitle($club['name']);
                        if (!isset($existingClub)) {
                            $clubUid = $this->clubrepository->createClub("", $club['name']);
                            $participant->setClubUid($clubUid);
                        } else {
                            $participant->setClubUid($existingClub->getClubUid());
                        }
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
                    $this->competitorService->createCredentialFor($competitor->getId(), $participant->getParticipantUid(), $participant->getStartnumber(), $registration['ref_nr']);
                }
            }
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

    /**
     * Handle DNS click from email
     *
     * @param string|null $participant_uid
     * @param string $currentuserUid
     * @return array
     * @throws BrevetException
     */
    public function participantclickeddnsinmail(?string $participant_uid, string $currentuserUid): bool
    {
        // Empty method to be implemented

        $participant =  $this->participantRepository->participantFor($participant_uid);

        if (!isset($participant)) {
            throw new BrevetException("Participant not found", 5, null);
        }
        $this->participantRepository->setDns($participant->getParticipantUid());



        return true;
    }

    public function checkoutAdmin(?string $participant_uid, ?string $checkpoint_uid, string $getAttribute): array
    {
        $participant = $this->participantRepository->participantFor($participant_uid);

        if (!isset($participant)) {
            throw new BrevetException("Cannot find participant", 5, null);
        }

        $checkpoint = $this->checkpointsService->checkpointFor($checkpoint_uid);

        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }

        $track = $this->trackRepository->getTrackByUid($participant->getTrackUid());

        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }

        // Check if the participant has checked in at this checkpoint
        $stampExists = $this->participantRepository->hasStampOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid);

        if (!$stampExists) {
            throw new BrevetException("Cannot checkout before checking in", 6, null);
        }

        // Check if this is a start or end checkpoint - for these, checkout time equals check-in time
        $isStart = $this->checkpointsService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());
        $isEnd = $this->checkpointsService->isEndCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        if ($isStart || $isEnd) {
            // For start/end checkpoints, checkout time should equal check-in time
            // Get current check-in time
            $checkpointStatus = $this->participantRepository->stampTimeOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid);
            if ($checkpointStatus && $checkpointStatus->getPassededDateTime()) {
                // Set checkout time equal to check-in time
                $this->participantRepository->checkoutFromCheckpointWithTime(
                    $participant->getParticipantUid(),
                    $checkpoint_uid,
                    1,
                    0,
                    $checkpointStatus->getPassededDateTime()
                );
            }
        } else {
            // For intermediate checkpoints, set current time as checkout time
            $this->participantRepository->checkoutFromCheckpoint($participant->getParticipantUid(), $checkpoint_uid, 1, 0);
        }

        return $this->randonneurservice->getChecpointsForRandonneurForAdmin($participant, $track);
    }

    public function rollbackCheckoutAdmin(?string $participant_uid, ?string $checkpoint_uid, string $getAttribute): bool
    {
        $participant = $this->participantRepository->participantFor($participant_uid);

        if (!isset($participant)) {
            throw new BrevetException("Cannot find participant", 5, null);
        }

        $checkpoint = $this->checkpointsService->checkpointFor($checkpoint_uid);

        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }

        // Use clearCheckoutTimeOnly for all checkpoint types to ensure check-in times are never removed
        return $this->participantRepository->clearCheckoutTimeOnly($participant->getParticipantUid(), $checkpoint_uid);
    }


    function generateHomologationCsv($track_uid)
    {
        $track = $this->trackRepository->getTrackByUid($track_uid);
        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }

        // Convert string date to DateTime object if needed
        $startDateTime = $track->getStartDateTime();
        if (is_string($startDateTime)) {
            $startDateTime = new \DateTime($startDateTime);
        }
        $date = $startDateTime->format('Y-m-d');
        $distance = $track->getDistance();

        // Get participants data using resultOnTrack
        $participants = $this->resultRepository->resultOnTrack($track_uid);



        $track_name = $track->getTitle();
        $organizing_clubID = $track->getOrganizerId();



        // Get organizer information
        $club = null;
        if ($organizing_clubID !== null) {
            $organizer = $this->organizerRepository->getOrganizerById($organizing_clubID);
            if ($organizer && $organizer->getClubUid() != null) {
                $club = $this->clubrepository->getClubByUid($organizer->getClubUid());
            }
        }

        if (!$club) {
            // Create a default club entry or throw an exception based on your business logic
            throw new BrevetException("No valid club found for this track", 5, null);
        }

        // Create a new CSV writer with proper UTF-8 encoding
        $startDateTime = $track->getStartDateTime();
        if (is_string($startDateTime)) {
            $startDateTime = new \DateTime($startDateTime);
        }
        $sanitizedTrackTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $track->getTitle());
        $filename = 'Homologation_' . $sanitizedTrackTitle . '_' . $startDateTime->format('Y-m-d') . '.csv';

        // Create CSV with UTF-8 BOM for proper encoding of Swedish characters
        $csv = Writer::createFromString('');
        $csv->setOutputBOM(Writer::BOM_UTF8);

        // Add the header rows
        $csv->insertOne(['', 'ORGANIZING CLUB', '', '', 'ACP code number', 'DATE', 'DISTANCE', 'INFORMATION', '', '']);
        $csv->insertOne(['', $club->getTitle() ?? 'Unknown Club', '', '', $club->getAcpKod() ?? 'N/A', $date, $distance . ' km', 'Medal', 'Gender', '']);
        $csv->insertOne(['Homologation number', 'LAST NAME', 'FIRST NAME', 'RIDER\'S CLUB', '', 'ACP CODE NUMBER', 'TIME', '(x)', '(F)', 'BIRTH DATE']);
        // Add participant data rows


        foreach ($participants as $participant) {


            // Get participant object to access competitor_uid
            $participantObj = $this->participantRepository->participantFor($participant['participant_uid']);

            // Skip participants with DNS or DNF status
            if ($participantObj->isDns() || $participantObj->isDnf()) {
                continue;
            }

            $competitor = $this->competitorService->getCompetitorByUid($participantObj->getCompetitorUid(), "");

            $csv->insertOne([
                $participant['brevenr'] === 0 ? '' : $participant['brevenr'], // Homologation number
                $participant['efternamn'], // Last name
                $participant['fornamn'], // First name
                $participant['klubb'], // Rider's club
                '', // Empty column
                $participant['brevenr'] == 0 ? '' : $participant['brevenr'], // ACP code number
                $participant['tid'] ?? '', // Time
                $participant['medal'] ? 'x' : '', // Medal (x)
                $competitor->getGender() == 1 ? 'F' : '', // Gender (F for female)
                $competitor->getBirthDate() // Birth date
            ]);
        }

        return [
            'filename' => $filename,
            'content' => $csv->toString()
        ];
    }

    function generateParticipantListCsv($track_uid)
    {
        $track = $this->trackRepository->getTrackByUid($track_uid);
        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }

        // Get participants for the track
        $participants = $this->participantRepository->participantsOnTrack($track_uid);

        if (empty($participants)) {
            throw new BrevetException("No participants found for this track", 5, null);
        }

        // Convert string date to DateTime object if needed
        $startDateTime = $track->getStartDateTime();
        if (is_string($startDateTime)) {
            $startDateTime = new \DateTime($startDateTime);
        }

        // Get organizer information
        $organizing_clubID = $track->getOrganizerId();
        $club = null;
        if ($organizing_clubID !== null) {
            $organizer = $this->organizerRepository->getOrganizerById($organizing_clubID);
            if ($organizer && $organizer->getClubUid() != null) {
                $club = $this->clubrepository->getClubByUid($organizer->getClubUid());
            }
        }

        // Create filename with sanitized track title
        $sanitizedTrackTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $track->getTitle());
        $filename = 'Participant_List_' . $sanitizedTrackTitle . '_' . $startDateTime->format('Y-m-d') . '.csv';

        // Create CSV with UTF-8 BOM for proper encoding of Swedish characters
        $csv = Writer::createFromString('');
        $csv->setOutputBOM(Writer::BOM_UTF8);

        // Add header information
        $csv->insertOne(['PARTICIPANT LIST']);
        $csv->insertOne(['Event:', $track->getTitle()]);
        $csv->insertOne(['Date:', $startDateTime->format('Y-m-d')]);
        $csv->insertOne(['Distance:', $track->getDistance() . ' km']);
        $csv->insertOne(['Organizing Club:', $club ? ($club->getTitle() ?? 'Unknown Club') : 'N/A']);
        $csv->insertOne(['ACP Code:', $club ? ($club->getAcpKod() ?? 'N/A') : 'N/A']);
        $csv->insertOne(['Total Participants:', count($participants)]);
        $csv->insertOne([]); // Empty row for spacing

        // Add column headers
        $csv->insertOne([
            'Start Number',
            'First Name',
            'Last Name',
            'Club',
            'Birth Date',
            'Email',
            'Phone',
            'Address',
            'Postal Code',
            'City',
            'Country',
            'Registration Date',
            'Medal',
            'Status'
        ]);

        // Add participant data
        foreach ($participants as $participant) {
            // Get competitor information
            $competitor = $this->competitorService->getCompetitorByUid($participant->getCompetitorUid(), "");

            // Get competitor info (contact details, address, etc.) - use repository directly to get full entity
            $competitorInfo = null;
            if ($competitor) {
                $competitorInfo = $this->competitorInfoRepository->getCompetitorInfoByCompetitorUid($competitor->getCompetitorUid());
            }

            // Get club information
            $club = null;
            if ($participant->getClubUid()) {
                $club = $this->clubrepository->getClubByUid($participant->getClubUid());
            }

            // Determine status
            $status = 'Registered';
            if ($participant->isDns()) {
                $status = 'DNS';
            } elseif ($participant->isDnf()) {
                $status = 'DNF';
            } elseif ($participant->isStarted()) {
                $status = 'Started';
            } elseif ($participant->isFinished()) {
                $status = 'Finished';
            }

            $csv->insertOne([
                $participant->getStartnumber(),
                $competitor ? $competitor->getGivenName() : '',
                $competitor ? $competitor->getFamilyName() : '',
                $club ? ($club->getTitle() ?? 'Unknown Club') : '',
                $competitor ? $competitor->getBirthDate() : '',
                $competitorInfo ? $competitorInfo->getEmail() : '',
                $competitorInfo ? $competitorInfo->getPhone() : '',
                $competitorInfo ? $competitorInfo->getAdress() : '',
                $competitorInfo ? $competitorInfo->getPostalCode() : '',
                $competitorInfo ? $competitorInfo->getPlace() : '',
                $competitorInfo ? $competitorInfo->getCountry() : '',
                $participant->getRegisterDateTime(),
                $participant->getMedal() ? 'Ja' : 'Nej',
                $status
            ]);
        }

        return [
            'filename' => $filename,
            'content' => $csv->toString()
        ];
    }
}

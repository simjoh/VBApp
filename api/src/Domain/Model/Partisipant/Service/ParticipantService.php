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
use App\common\Service\EmailService;
use App\common\Service\LoggerService;
use App\Domain\Model\Track\Service\TrackService;
use PDO;
use PDOException;

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
    private $connection;
    private $emailService;
    private $trackService;
    private $logger;

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
        OrganizerRepository $organizerRepository,
        PDO $connection,
        EmailService $emailService,
        TrackService $trackService,
        LoggerService $logger
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
        $this->connection = $connection;
        $this->emailService = $emailService;
        $this->trackService = $trackService;
        $this->logger = $logger;
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
            $club_uid = $this->createClubAndReturnUid($club->getAcpKod(), $club->getTitle());
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

        // Check if track is active before proceeding with upload
        if (!$track->isActive()) {
            throw new BrevetException("Banan är inte aktiv. Upload av deltagare är inte tillåtet för inaktiva banor.", 403, null);
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
        $uploadStats = [
            'total_rows' => 0,
            'successful' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => [],
            'participants' => []
        ];

        $rowNumber = 0;
        foreach ($records as $record) {
            $rowNumber++;
            $uploadStats['total_rows']++;

            try {
                // Validate required fields
                if (empty($record[0]) || empty($record[1]) || empty($record[2])) {
                    $uploadStats['failed']++;
                    $uploadStats['errors'][] = [
                        'row' => $rowNumber,
                        'message' => 'Missing required fields: start number, first name, or last name',
                        'data' => $record
                    ];
                    continue;
                }

                // Convert birth year to proper date format (YYYY-01-01)
                $birthYear = $record[12];
                if (empty($birthYear) || !is_numeric($birthYear)) {
                    $uploadStats['failed']++;
                    $uploadStats['errors'][] = [
                        'row' => $rowNumber,
                        'message' => 'Invalid birth year: ' . $birthYear,
                        'data' => $record
                    ];
                    continue;
                }
                $birthDate = $birthYear . '-01-01'; // Use January 1st as default date

                // Check if start number is unique on this track
                $startNumber = $record[0];
                $existingParticipantWithStartNumber = $this->participantRepository->participantOntRackAndStartNumber($trackUid, $startNumber);
                if (isset($existingParticipantWithStartNumber)) {
                    $uploadStats['failed']++;
                    $uploadStats['errors'][] = [
                        'row' => $rowNumber,
                        'message' => 'Start number already exists on this track: ' . $startNumber,
                        'data' => $record
                    ];
                    continue;
                }

                // Check if reference number is unique (if provided)
                $referenceNumber = isset($record[13]) ? trim($record[13]) : null;
                if (!empty($referenceNumber)) {
                    $existingParticipantWithRefNr = $this->participantRepository->participantOnTrackAndRefNr($trackUid, $referenceNumber);
                    if (isset($existingParticipantWithRefNr)) {
                        $uploadStats['failed']++;
                        $uploadStats['errors'][] = [
                            'row' => $rowNumber,
                            'message' => 'Reference number already exists on this track: ' . $referenceNumber,
                            'data' => $record
                        ];
                        continue;
                    }
                }

                // Process physical brevet card preference (column 14, index 14)
                $physicalBrevetCard = false;
                if (isset($record[14]) && !empty($record[14])) {
                    $physicalBrevetCard = in_array(strtolower(trim($record[14])), ['ja', 'yes', '1', 'true']);
                }

                // Process additional information (column 15, index 15)
                $additionalInformation = isset($record[15]) ? trim($record[15]) : null;

                // se om det finns en sådan deltagare först
                $competitor = $this->competitorService->getCompetitorByNameAndBirthDate($record[1], $record[2], $birthDate);

                if (!isset($competitor)) {
                    // createOne
                    $competitor = $this->competitorService->createCompetitor($record[1], $record[2], "", $birthDate);
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
                        $clubUid = $this->createClubAndReturnUid("", $record[4]);
                        $participant->setClubUid($clubUid);
                    } else {
                        $participant->setClubUid($existingClub->getClubUid());
                    }
                    $participant->setTrackUid($trackUid);
                    $participant->setRegisterDateTime($record[11]);
                    
                    // Set physical brevet card preference and additional information
                    $participant->setUsePhysicalBrevetCard($physicalBrevetCard);
                    $participant->setAdditionalInformation($additionalInformation);

                    $participantcreated = $this->participantRepository->createparticipant($participant);
                    if (isset($participantcreated)) {

                        $this->participantRepository->createTrackCheckpointsFor($participant, $this->trackRepository->checkpoints($trackUid));
                    }

                    array_push($createdParticipants, $participant);
                    $uploadStats['successful']++;
                } else {
                    $uploadStats['skipped']++;
                    $uploadStats['errors'][] = [
                        'row' => $rowNumber,
                        'message' => 'Participant already exists on this track: ' . $record[1] . ' ' . $record[2],
                        'data' => $record
                    ];
                }

                if (isset($participantcreated) && isset($competitor)) {
                    // skapa upp inloggning för cyklisten
                    $this->competitorService->createCredentialFor($competitor->getId(), $participant->getParticipantUid(), $record[0], $record[13]);
                }

            } catch (Exception $e) {
                $uploadStats['failed']++;
                $uploadStats['errors'][] = [
                    'row' => $rowNumber,
                    'message' => 'Error processing row: ' . $e->getMessage(),
                    'data' => $record
                ];
            }
        }

        // Add created participants to stats
        $participantRepresentations = $this->participantassembly->toRepresentations($createdParticipants, $currentUserUid);
        
        // Add competitor information to each participant
        foreach ($participantRepresentations as $participantRep) {
            $competitor = $this->competitorService->getCompetitorByUid($participantRep->getCompetitorUid(), $currentUserUid);
            if ($competitor) {
                $participantRep->setCompetitor($competitor);
            }
        }
        
        $uploadStats['participants'] = $participantRepresentations;

        return $uploadStats;
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
            $participant->setFinishedTimestamp(date('Y-m-d H:i:s'));
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
            $participant->setFinishedTimestamp(date('Y-m-d H:i:s'));
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
        // Log the start of participant creation from loppservice
        if (isset($this->logger)) {
            $this->logger->info('Starting participant creation from loppservice', [
                'track_uid' => $track_uid,
                'person_uid' => $loppservicePersonRepresentation->person_uid ?? 'unknown',
                'registration_uid' => $loppserviceRegistrationRepresentation->registration['registration_uid'] ?? 'unknown'
            ]);
        }

        $finnsitabell = GlobalConfig::get($track_uid);

        if ($finnsitabell) {
            $track_uid = $finnsitabell;
        }

        if ($track_uid == 'ecc0fccc-ced8-493d-b671-e3379e2f5743') {
            $track_uid = '15689abe-ebdd-459a-8209-5b04815af486';
        }

        // TEST ERROR - Force an error for testing error handling
   
        //   throw new BrevetException("TEST ERROR2: Simulated error in ParticipantService for testing error event creation", 999, null);
        

        try {
            $registration = $loppserviceRegistrationRepresentation->registration;
            $participant_to_create = $loppserviceRegistrationRepresentation->participant;
            $contactinformation = $participant_to_create['contactinformation'];
            $adress = $participant_to_create['adress'];

            $track = $this->trackRepository->getTrackByUid($track_uid);
            if (!isset($track)) {
                if (isset($this->logger)) {
                    $this->logger->error('Track not found during participant creation', [
                        'track_uid' => $track_uid,
                        'person_uid' => $person_uid ?? 'unknown'
                    ]);
                }
                throw new BrevetException("Track not exists", 5, null);
            }

            $registration_uid = $registration['registration_uid'];
            $person_uid = $participant_to_create['person_uid'];

            if (!isset($registration)) {
                throw new BrevetException("No registration found", 5, null);
            }

            // Check if this person_uid already has a participant on this track
            if ($this->participantRepository->hasExistingRegistration($person_uid, $track_uid)) {
                throw new BrevetException("This competitor is already registered for this track with person_uid: " . $person_uid, 5, null);
            }

            // Check if participant_uid (registration_uid) already exists
            $existingParticipant = $this->participantRepository->participantFor($registration_uid);
            if ($existingParticipant) {
                throw new BrevetException("A participant already exists with registration_uid: " . $registration_uid, 5, null);
            }

            // Try to find existing competitor by person_uid (which becomes competitor_uid)
            $competitor = $this->competitorService->getCompetitorByUid3($person_uid, "");
            
            if (!isset($competitor)) {
                // Competitor doesn't exist, create a new one with person_uid as competitor_uid
                $competitor = $this->competitorService->createCompetitorFromLoppservice(
                    $participant_to_create['firstname'], 
                    $participant_to_create['surname'], 
                    "", 
                    $participant_to_create['birthdate'], 
                    $person_uid, 
                    $participant_to_create['gender']
                );
                
                if (!$competitor) {
                    if (isset($this->logger)) {
                        $this->logger->error('Failed to create competitor', [
                            'person_uid' => $person_uid,
                            'firstname' => $participant_to_create['firstname'],
                            'surname' => $participant_to_create['surname']
                        ]);
                    }
                    throw new BrevetException("Failed to create competitor for person_uid: " . $person_uid, 5, null);
                }

                // Create competitor info for the new competitor
                $this->competitorInfoRepository->creatCompetitorInfoForCompetitorParamsFromLoppservice(
                    $contactinformation['email'], 
                    $contactinformation['tel'], 
                    $adress['adress'], 
                    $adress['postal_code'], 
                    $adress['city'], 
                    $this->countryrepository->countryFor($adress['country_id'])->country_name_sv, 
                    $person_uid, 
                    $adress['country_id']
                );
            }

            // Handle club assignment
            $clubUid = null;
            if (isset($club) && !empty($club)) {
                // Try to find existing club by club_uid first
                $existingClub = null;
                if (isset($club['club_uid']) && !empty($club['club_uid'])) {
                    $existingClub = $this->clubrepository->getClubByUId($club['club_uid']);
                }
                
                // If not found by club_uid, try by name
                if (!isset($existingClub) && isset($club['name']) && !empty($club['name'])) {
                    $existingClub = $this->clubrepository->getClubByTitle($club['name']);
                }
                
                // If still not found, try to fetch from loppservice and create with same UID
                if (!isset($existingClub) && isset($club['club_uid']) && !empty($club['club_uid'])) {
                    $clubUid = $this->syncClubFromLoppservice($club['club_uid'], $club['name'] ?? '');
                }
                
                // If still not found, create new club with new UUID
                if (!isset($existingClub) && !isset($clubUid) && isset($club['name']) && !empty($club['name'])) {
                    $clubUid = $this->createClubAndReturnUid("", $club['name']);
                }
                
                // Use existing club UID if found
                if (isset($existingClub)) {
                    $clubUid = $existingClub->getClubUid();
                }
            }

            // Create the participant
            $participant = new Participant();
            $participant->setCompetitorUid($person_uid); // Use person_uid as competitor_uid
            $participant->setStartnumber($registration['startnumber']);
            $participant->setParticipantUid($registration_uid); // Use registration_uid as participant_uid
            $participant->setFinished(false);
            $participant->setTrackUid($track->getTrackUid());
            $participant->setDnf(false);
            $participant->setDns(false);
            $participant->setTime(null);
            $participant->setStarted(false);
            $participant->setAcpkod("s");
            $participant->setMedal($medal ?? false);
            $participant->setClubUid($clubUid);
            $participant->setRegisterDateTime(new DateTime($registration['created_at']));
            $participant->setAdditionalInformation($registration['additional_information'] ?? null);
            $participant->setUsePhysicalBrevetCard($registration['use_physical_brevet_card'] ?? false);

            // Create the participant record
            $participantcreated = $this->participantRepository->createparticipant($participant);
            
            if (!isset($participantcreated)) {
                if (isset($this->logger)) {
                    $this->logger->error('Failed to create participant record', [
                        'registration_uid' => $registration_uid,
                        'person_uid' => $person_uid,
                        'track_uid' => $track_uid,
                        'startnumber' => $registration['startnumber']
                    ]);
                }
                throw new BrevetException("Failed to create participant record", 5, null);
            }

            // Create participant checkpoints
            $this->participantRepository->createTrackCheckpointsFor($participant, $this->trackRepository->checkpoints($track->getTrackUid()));

            // Create competitor credentials
            if (isset($competitor)) {
                $this->competitorService->createCredentialFor(
                    $competitor->getId(), 
                    $participant->getParticipantUid(), 
                    $participant->getStartnumber(), 
                    $registration['ref_nr']
                );
            }

            return true;
            
        } catch (BrevetException $e) {
            // Re-throw BrevetException as-is
            throw $e;
        } catch (Exception $e) {
            if (isset($this->logger)) {
                $this->logger->error('Unexpected error during participant creation from loppservice', [
                    'error' => $e->getMessage(),
                    'file' => $e->getFile(),
                    'line' => $e->getLine(),
                    'track_uid' => $track_uid ?? 'unknown',
                    'person_uid' => $person_uid ?? 'unknown',
                    'registration_uid' => $registration_uid ?? 'unknown'
                ]);
            }
            throw new BrevetException("Unexpected error: " . $e->getMessage() . " at line " . $e->getLine() . " in " . $e->getFile(), 5, null);
        }
    }

    private function validateInput($datatovalidate, $trackuid): bool
    {
        if (is_null($trackuid)) {
            throw new BrevetException("Track uid must be set", 5, null);
        }

        if (is_null($datatovalidate->registration['registration_uid'])) {
            throw new BrevetException("Registration uid must be set", 5, null);
        }

        if (is_null($datatovalidate->participant)) {
            throw new BrevetException("Participant must be set", 5, null);
        }

        if (is_null($datatovalidate->participant['person_uid'])) {
            throw new BrevetException("Person uid must be set", 5, null);
        }

        // Check basic participant data
        if (!isset($datatovalidate->participant['adress']) || !isset($datatovalidate->participant['contactinformation'])) {
            throw new BrevetException("Address and contact information must be provided", 5, null);
        }

        // Check contact information
        $contactInfo = $datatovalidate->participant['contactinformation'];
        if (!isset($contactInfo['email']) || empty($contactInfo['email'])) {
            throw new BrevetException("Email must be provided", 5, null);
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
            $clubforparticipant = $this->clubrepository->getClubByUid($participantObj->getClubUid());

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
            
                $clubforparticipant->getAcpKod() ?? '', // Empty column
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
            'Status',
            'Additional Information',
            'Use Physical Brevet Card'
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
                $status,
                $participant->getAdditionalInformation() ?? '',
                $participant->getUsePhysicalBrevetCard() ? 'Ja' : 'Nej'
            ]);
        }

        return [
            'filename' => $filename,
            'content' => $csv->toString()
        ];
    }

    public function getParticipantStats(string $date): array
    {
        try {
            // Get daily stats
            $dailyStats = $this->getDailyStats($date);
            
            // Get weekly stats (last 7 days including today)
            $weeklyStats = $this->getWeeklyStats($date);

            // Get yearly stats
            $yearlyStats = $this->getYearlyStats($date);

            // Get latest registration
            $latestRegistration = $this->getLatestRegistration();
            
            return [
                'daily' => $dailyStats,
                'weekly' => $weeklyStats,
                'yearly' => $yearlyStats,
                'latest_registration' => $latestRegistration
            ];
        } catch (PDOException $e) {
            error_log("Error getting participant stats: " . $e->getMessage());
            return [
                'daily' => [
                    'countparticipants' => 0,
                    'started' => 0,
                    'completed' => 0,
                    'dnf' => 0,
                    'dns' => 0
                ],
                'weekly' => [
                    'countparticipants' => 0,
                    'started' => 0,
                    'completed' => 0,
                    'dnf' => 0,
                    'dns' => 0
                ],
                'yearly' => [
                    'countparticipants' => 0,
                    'started' => 0,
                    'completed' => 0,
                    'dnf' => 0,
                    'dns' => 0
                ],
                'latest_registration' => null
            ];
        }
    }

    private function getDailyStats(string $date): array
    {
        $sql = "SELECT 
                COUNT(DISTINCT CASE WHEN DATE(p.register_date_time) = DATE(:date) THEN p.participant_uid END) as countparticipants,
                COALESCE(SUM(CASE WHEN DATE(p.register_date_time) = DATE(:date) AND p.started = 1 THEN 1 ELSE 0 END), 0) as started,
                COALESCE(SUM(CASE WHEN DATE(p.finished_timestamp) = DATE(:date) THEN 1 ELSE 0 END), 0) as completed,
                COALESCE(SUM(CASE WHEN DATE(p.dnf_timestamp) = DATE(:date) THEN 1 ELSE 0 END), 0) as dnf,
                COALESCE(SUM(CASE WHEN DATE(p.dns_timestamp) = DATE(:date) THEN 1 ELSE 0 END), 0) as dns
            FROM participant p
            JOIN track t ON t.track_uid = p.track_uid";

        error_log("Daily stats query for date: " . $date);
        error_log("SQL: " . $sql);

        $stmt = $this->connection->prepare($sql);
        $stmt->execute(['date' => $date]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    private function getWeeklyStats(string $date): array
    {
        $sql = "SELECT 
                COUNT(DISTINCT CASE WHEN p.register_date_time >= DATE_SUB(:date, INTERVAL 6 DAY) AND p.register_date_time < DATE_ADD(DATE(:date), INTERVAL 1 DAY) THEN p.participant_uid END) as countparticipants,
                COALESCE(SUM(CASE WHEN p.register_date_time >= DATE_SUB(:date, INTERVAL 6 DAY) AND p.register_date_time < DATE_ADD(DATE(:date), INTERVAL 1 DAY) AND p.started = 1 THEN 1 ELSE 0 END), 0) as started,
                COALESCE(SUM(CASE WHEN p.finished_timestamp >= DATE_SUB(:date, INTERVAL 6 DAY) AND p.finished_timestamp < DATE_ADD(DATE(:date), INTERVAL 1 DAY) THEN 1 ELSE 0 END), 0) as completed,
                COALESCE(SUM(CASE WHEN p.dnf_timestamp >= DATE_SUB(:date, INTERVAL 6 DAY) AND p.dnf_timestamp < DATE_ADD(DATE(:date), INTERVAL 1 DAY) THEN 1 ELSE 0 END), 0) as dnf,
                COALESCE(SUM(CASE WHEN p.dns_timestamp >= DATE_SUB(:date, INTERVAL 6 DAY) AND p.dns_timestamp < DATE_ADD(DATE(:date), INTERVAL 1 DAY) THEN 1 ELSE 0 END), 0) as dns
            FROM participant p
            JOIN track t ON t.track_uid = p.track_uid";

        error_log("Weekly stats query for date: " . $date);
        error_log("SQL: " . $sql);
        
        $statement = $this->connection->prepare($sql);
        $statement->bindParam(':date', $date);
        $statement->execute();
        
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        error_log("Weekly stats result: " . json_encode($result));
        
        return $result ?: [
            'countparticipants' => 0,
            'started' => 0,
            'completed' => 0,
            'dnf' => 0,
            'dns' => 0
        ];
    }

    private function getYearlyStats(string $date): array
    {
        $sql = "SELECT 
                COUNT(DISTINCT CASE WHEN YEAR(p.register_date_time) = YEAR(:date) THEN p.participant_uid END) as countparticipants,
                COALESCE(SUM(CASE WHEN YEAR(p.register_date_time) = YEAR(:date) AND p.started = 1 THEN 1 ELSE 0 END), 0) as started,
                COALESCE(SUM(CASE WHEN YEAR(p.finished_timestamp) = YEAR(:date) THEN 1 ELSE 0 END), 0) as completed,
                COALESCE(SUM(CASE WHEN YEAR(p.dnf_timestamp) = YEAR(:date) THEN 1 ELSE 0 END), 0) as dnf,
                COALESCE(SUM(CASE WHEN YEAR(p.dns_timestamp) = YEAR(:date) THEN 1 ELSE 0 END), 0) as dns
            FROM participant p
            JOIN track t ON t.track_uid = p.track_uid";

        error_log("Yearly stats query for date: " . $date);
        error_log("SQL: " . $sql);
        
        $statement = $this->connection->prepare($sql);
        $statement->bindParam(':date', $date);
        $statement->execute();
        
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        error_log("Yearly stats result: " . json_encode($result));
        
        return $result ?: [
            'countparticipants' => 0,
            'started' => 0,
            'completed' => 0,
            'dnf' => 0,
            'dns' => 0
        ];
    }

    private function getLatestRegistration(): ?array
    {
        $sql = "SELECT 
                p.*,
                c.given_name,
                c.family_name,
                cl.title as club_name,
                t.title as track_name
            FROM participant p
            JOIN track t ON t.track_uid = p.track_uid
            LEFT JOIN competitors c ON c.competitor_uid = p.competitor_uid
            LEFT JOIN club cl ON cl.club_uid = p.club_uid
            ORDER BY p.register_date_time DESC
            LIMIT 1";

        $statement = $this->connection->prepare($sql);
        $statement->execute();
        
        $result = $statement->fetch(PDO::FETCH_ASSOC);
        
        if (!$result) {
            return null;
        }

        return [
            'participant_uid' => $result['participant_uid'],
            'name' => $result['given_name'] . ' ' . $result['family_name'],
            'club' => $result['club_name'] ?? 'No Club',
            'track' => $result['track_name'],
            'registration_time' => $result['register_date_time']
        ];
    }

    public function getTopTracks(): array
    {
        try {
            $sql = "WITH recent_registrations AS (
                SELECT 
                    t.track_uid,
                    t.title as track_name,
                    COUNT(DISTINCT p.participant_uid) as total_participants,
                    COUNT(DISTINCT CASE WHEN p.register_date_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN p.participant_uid END) as recent_registrations,
                    MIN(CASE WHEN p.register_date_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN p.register_date_time END) as recent_first_registration,
                    MAX(CASE WHEN p.register_date_time >= DATE_SUB(CURDATE(), INTERVAL 30 DAY) THEN p.register_date_time END) as recent_last_registration,
                    o.organization_name as organizer_name,
                    t.active
                FROM track t
                LEFT JOIN participant p ON t.track_uid = p.track_uid
                LEFT JOIN organizers o ON t.organizer_id = o.id
                GROUP BY t.track_uid, t.title, o.organization_name, t.active
            )
            SELECT 
                track_name,
                total_participants as participant_count,
                recent_registrations as registrations_last_30_days,
                recent_first_registration as first_registration,
                recent_last_registration as last_registration,
                organizer_name,
                active
            FROM recent_registrations 
            WHERE recent_registrations > 0
            ORDER BY total_participants DESC
            LIMIT 5";

            error_log("Executing top tracks query: " . $sql);
            
            $statement = $this->connection->prepare($sql);
            $statement->execute();
            
            $result = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            // Log the raw result
            error_log("Raw top tracks result: " . json_encode($result));
            
            return $result;
            
        } catch (\Exception $e) {
            error_log("Error in getTopTracks: " . $e->getMessage());
            return [];
        }
    }

    /**
     * Create a club and return its UID
     * 
     * @param string $acpKod The ACP code for the club
     * @param string $title The title/name of the club
     * @return string The club UID
     */
    private function createClubAndReturnUid(string $acpKod, string $title): string
    {
        $club = new \App\Domain\Model\Club\Club();
        $club->setClubUid(\Ramsey\Uuid\Uuid::uuid4()->toString());
        $club->setTitle($title);
        $club->setAcpKod($acpKod);
        
        $this->clubrepository->createClub($club);
        
        return $club->getClubUid();
    }

    /**
     * Sync a club from loppservice to the app database with the same UID
     * 
     * @param string $clubUid The club UID from loppservice
     * @param string $clubName The club name from loppservice
     * @return string|null The club UID if successful, null otherwise
     */
    private function syncClubFromLoppservice(string $clubUid, string $clubName = ''): ?string
    {
        try {
            // Create club in app database with same UID as loppservice
            $club = new \App\Domain\Model\Club\Club();
            $club->setClubUid($clubUid);
            $club->setTitle($clubName ?: 'Unknown Club');
            $club->setAcpKod("");
            
            $this->clubrepository->createClub($club);
            return $clubUid;
            
        } catch (\Exception $e) {
            // Log error but don't fail the registration
            error_log("Failed to sync club from loppservice: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Move a participant from one track to another
     * This includes validation and updating participant checkpoints
     */
    public function moveParticipantToTrack(string $participant_uid, string $new_track_uid, string $currentUserUid): ?ParticipantRepresentation
    {
        // Get permissions
        $permissions = $this->getPermissions($currentUserUid);
        
        // Get the participant to verify it exists
        $participant = $this->participantRepository->participantFor($participant_uid);
        if (!$participant) {
            throw new BrevetException("Participant not found with UID: " . $participant_uid, 404);
        }
        
        $old_track_uid = $participant->getTrackUid();
        
        // Validate that the new track exists
        $newTrack = $this->trackRepository->getTrackByUid($new_track_uid);
        if (!$newTrack) {
            throw new BrevetException("Target track not found with UID: " . $new_track_uid, 404);
        }
        
        // Check if participant has already started (has stamps, DNF, DNS, or finished)
        if ($participant->isStarted() || $participant->isFinished() || $participant->isDnf() || $participant->isDns()) {
            throw new BrevetException("Cannot move participant who has already started, finished, DNF, or DNS", 9);
        }
        
        // Check if the participant is already on the target track
        if ($old_track_uid === $new_track_uid) {
            throw new BrevetException("Participant is already on the target track", 9);
        }
        
        // Check if there's already a participant with the same start number on the target track
        $existingParticipant = $this->participantRepository->participantOntRackAndStartNumber($new_track_uid, $participant->getStartnumber());
        if ($existingParticipant) {
            throw new BrevetException("A participant with start number " . $participant->getStartnumber() . " already exists on the target track", 9);
        }
        
        // Store old start number for email notification
        $oldStartNumber = $participant->getStartnumber();
        
        // Move the participant to the new track
        $participant->setTrackUid($new_track_uid);
        $newStartNumber = $this->findNextAvailableStartNumber($new_track_uid);
        $participant->setStartnumber($newStartNumber);
        
        $updatedParticipant = $this->participantRepository->updateParticipant($participant);
        if (!$updatedParticipant) {
            throw new BrevetException("Failed to update participant", 5);
        }
        
        // Send email notification if start number changed
        if ($oldStartNumber !== $newStartNumber) {
            $this->sendStartNumberChangeNotification($participant_uid, $oldStartNumber, $newStartNumber);
        }
        
        // Use the existing moveParticipantToTrack method to handle checkpoint recreation
        $success = $this->participantRepository->moveParticipantToTrack($participant_uid, $new_track_uid);
        if (!$success) {
            throw new BrevetException("Failed to move participant checkpoints", 5);
        }
        
        return $this->participantassembly->toRepresentation($updatedParticipant, $permissions, $currentUserUid);
    }

    /**
     * Move all participants from one track to another track
     * This includes validation and updating participant checkpoints
     */
    public function moveAllParticipantsToTrack(string $from_track_uid, string $to_track_uid, string $currentUserUid): array
    {
        // Get permissions
        $permissions = $this->getPermissions($currentUserUid);
        
        // Validate that the source track exists
        $sourceTrack = $this->trackRepository->getTrackByUid($from_track_uid);
        if (!$sourceTrack) {
            throw new BrevetException("Source track not found with UID: " . $from_track_uid, 404);
        }
        
        // Validate that the target track exists
        $targetTrack = $this->trackRepository->getTrackByUid($to_track_uid);
        if (!$targetTrack) {
            throw new BrevetException("Target track not found with UID: " . $to_track_uid, 404);
        }
        
        // Check if the tracks are the same
        if ($from_track_uid === $to_track_uid) {
            throw new BrevetException("Source and target tracks are the same", 9);
        }
        
        // Move all participants using the repository method
        $results = $this->participantRepository->moveAllParticipantsToTrack($from_track_uid, $to_track_uid);
        
        return $results;
    }

    /**
     * Resolve start number conflict by moving a participant to a new track with an automatically assigned start number
     */
    public function resolveStartnumberConflict(string $participant_uid, string $to_track_uid, string $currentUserUid): ?ParticipantRepresentation
    {
        // Get permissions
        $permissions = $this->getPermissions($currentUserUid);
        
        // Get the participant to verify it exists
        $participant = $this->participantRepository->participantFor($participant_uid);
        if (!$participant) {
            throw new BrevetException("Participant not found with UID: " . $participant_uid, 404);
        }
        
        // Validate that the target track exists
        $targetTrack = $this->trackRepository->getTrackByUid($to_track_uid);
        if (!$targetTrack) {
            throw new BrevetException("Target track not found with UID: " . $to_track_uid, 404);
        }
        
        // Check if participant can be moved (not started, DNF, DNS, or finished)
        if ($participant->isStarted() || $participant->isFinished() || $participant->isDnf() || $participant->isDns()) {
            throw new BrevetException("Cannot move participant who has already started, finished, DNF, or DNS", 9);
        }
        
        // Store old start number for email notification
        $oldStartNumber = $participant->getStartnumber();
        
        // Automatically find the next available start number (no client control)
        $new_startnumber = $this->findNextAvailableStartNumber($to_track_uid);
        
        // Move the participant with the automatically assigned start number
        $participant->setTrackUid($to_track_uid);
        $participant->setStartnumber($new_startnumber);
        
        $updatedParticipant = $this->participantRepository->updateParticipant($participant);
        if (!$updatedParticipant) {
            throw new BrevetException("Failed to update participant", 5);
        }
        
        // Send email notification if start number changed
        if ($oldStartNumber !== $new_startnumber) {
            $this->sendStartNumberChangeNotification($participant_uid, $oldStartNumber, $new_startnumber);
        }
        
        // Use the existing moveParticipantToTrack method to handle checkpoint recreation
        $success = $this->participantRepository->moveParticipantToTrack($participant_uid, $to_track_uid);
        if (!$success) {
            throw new BrevetException("Failed to move participant checkpoints", 5);
        }
        
        return $this->participantassembly->toRepresentation($updatedParticipant, $permissions, $currentUserUid);
    }
    
    /**
     * Find the next available start number on a track by finding gaps in the sequence
     * If there are gaps (e.g., 1001, 1003, 1004), it will return 1002
     * If no gaps, it will return the next number after the highest
     * Handles cases where start numbers begin at higher values (e.g., 4001, 4002, 4003)
     */
    private function findNextAvailableStartNumber(string $track_uid): string
    {
        $participants = $this->participantRepository->participantsOnTrack($track_uid);
        $usedStartNumbers = [];
        
        foreach ($participants as $participant) {
            $usedStartNumbers[] = (int)$participant->getStartnumber();
        }
        
        if (empty($usedStartNumbers)) {
            return "1001"; // Default start number if no participants exist
        }
        
        // Sort the used start numbers
        sort($usedStartNumbers);
        
        // Find the actual sequence range
        $minNumber = $usedStartNumbers[0];
        $maxNumber = $usedStartNumbers[count($usedStartNumbers) - 1];
        
        // If the sequence starts at a higher number (e.g., 4001), work within that range
        // Otherwise, start from 1001 as the minimum
        $sequenceStart = max(1001, $minNumber);
        
        // Find the first gap in the sequence starting from the sequence start
        $expectedNumber = $sequenceStart;
        
        foreach ($usedStartNumbers as $usedNumber) {
            if ($usedNumber > $expectedNumber) {
                // Found a gap, return the expected number
                return (string)$expectedNumber;
            }
            $expectedNumber = $usedNumber + 1;
        }
        
        // No gaps found, return the next number after the highest
        return (string)$expectedNumber;
    }

    /**
     * Send email notification when a participant's start number changes
     * 
     * @param string $participant_uid The participant UID
     * @param string $oldStartNumber The old start number
     * @param string $newStartNumber The new start number
     * @return bool Whether the email was sent successfully
     */
    private function sendStartNumberChangeNotification(string $participant_uid, string $oldStartNumber, string $newStartNumber): bool
    {
        try {
            // Get participant with all related data
            $participant = $this->participantRepository->participantFor($participant_uid);
            if (!$participant) {
                error_log("Participant not found for email notification: " . $participant_uid);
                return false;
            }

            // Get track information
            $track = $this->trackService->getTrackByUid($participant->getTrackUid());
            if (!$track) {
                error_log("Track not found for email notification: " . $participant->getTrackUid());
                return false;
            }

            // Get competitor info for email
            $competitor = $this->competitorService->getCompetitorByUid($participant->getCompetitorUid(), "");
            if (!$competitor) {
                error_log("Competitor not found for email notification: " . $participant_uid);
                return false;
            }

            $competitorInfo = $this->competitorInfoRepository->getCompetitorInfoByCompetitorUid($competitor->getCompetitorUid());
            if (!$competitorInfo || !$competitorInfo->getEmail()) {
                error_log("No email found for participant: " . $participant_uid);
                return false;
            }

            // Get organizer information if available
            $organizer = "Arrangör";
            if ($track->getOrganizerId()) {
                $organizerObj = $this->organizerRepository->getOrganizerById($track->getOrganizerId());
                if ($organizerObj) {
                    $organizer = $organizerObj->getOrganizationName();
                }
            }

            // Generate a new reference number and store it
            $refNr = $this->competitorService->generateAndStoreRefNrForParticipant($participant->getParticipantUid(), $newStartNumber);
            
            // Prepare email data
            $emailData = [
                'participant' => $participant,
                'track' => $track,
                'organizer' => $organizer,
                'oldStartNumber' => $oldStartNumber,
                'newStartNumber' => $newStartNumber,
                'competitor' => $competitor,
                'competitorInfo' => $competitorInfo,
                'refNr' => $refNr
            ];

            // Send email - check if we're in development (mailhog) or production mode
            $subject = "Startnummer ändrat - " . $track->getTitle();
            
            // Get mail configuration
            $mailHost = $this->settings['mail']['mail_host'] ?? 'mailhog';
            $recipientEmail = $competitorInfo->getEmail();
            
            // If mailhog is configured, send to mailhog for testing, otherwise send to actual competitor
            if ($mailHost === 'mailhog') {
                $recipientEmail = 'receiverinbox@mailhog.local';
                error_log("Development mode: Start number change email sent to mailhog (would be sent to: " . $competitorInfo->getEmail() . ")");
            } else {
                error_log("Production mode: Start number change email sent to competitor: " . $competitorInfo->getEmail());
            }
            
            $success = $this->emailService->sendEmailWithTemplate(
                $recipientEmail,
                $subject,
                'startnumber-changed-email-template.php',
                $emailData
            );

            if ($success) {
                error_log("Start number change notification sent successfully to: " . $competitorInfo->getEmail());
            } else {
                error_log("Failed to send start number change notification to: " . $competitorInfo->getEmail());
            }

            return $success;

        } catch (\Exception $e) {
            error_log("Error sending start number change notification: " . $e->getMessage());
            return false;
        }
    }
}

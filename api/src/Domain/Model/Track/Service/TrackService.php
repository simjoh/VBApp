<?php

namespace App\Domain\Model\Track\Service;


use App\common\Exceptions\BrevetException;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\CheckPoint\Checkpoint;
use App\Domain\Model\Checkpoint\Repository\CheckpointRepository;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Event\Event;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Site\Repository\SiteRepository;
use App\Domain\Model\Site\Site;
use App\Domain\Model\Track\Bana;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Model\Track\Rest\RusaPlannerInputRepresentation;
use App\Domain\Model\Track\Rest\TrackAssembly;
use App\Domain\Model\Track\Rest\TrackRepresentation;
use App\Domain\Model\Track\Track;
use App\Domain\Model\Track\TracksForEvents;
use App\Domain\Permission\PermissionRepository;
use Equip\Structure\Dictionary;
use League\Csv\Reader;
use League\Csv\Statement;
use PrestaShop\Decimal\DecimalNumber;
use Psr\Container\ContainerInterface;
use stdClass;

class TrackService extends ServiceAbstract
{

    private $trackRepository;
    private $checkpointService;
    private $permissionrepository;
    private $trackAssembly;
    private $settings;
    private $siteRepository;
    private $eventRepository;
    private $checkpointRepository;
    private $participantRepository;
    private $rusaTimeTrackPlannerService;

    public function __construct(ContainerInterface    $c,
                                TrackRepository       $trackRepository,
                                CheckpointsService    $checkpointService,
                                PermissionRepository  $permissionRepository,
                                TrackAssembly         $trackAssembly,
                                SiteRepository        $siteRepository,
                                EventRepository       $eventRepository,
                                CheckpointRepository  $checkpointRepository,
                                ParticipantRepository $participantRepository, RusaTimeTrackPlannerService $rusaTimeTrackPlannerService)
    {
        $this->trackRepository = $trackRepository;
        $this->checkpointService = $checkpointService;
        $this->permissionrepository = $permissionRepository;
        $this->trackAssembly = $trackAssembly;
        $this->settings = $c->get('settings');
        $this->siteRepository = $siteRepository;
        $this->eventRepository = $eventRepository;
        $this->checkpointRepository = $checkpointRepository;
        $this->participantRepository = $participantRepository;
        $this->rusaTimeTrackPlannerService = $rusaTimeTrackPlannerService;
    }

    public function allTracks(string $currentuserUid): array
    {
        $permissions = $this->getPermissions($currentuserUid);
        $trackArray = $this->trackRepository->allTracks();
        // hämta checkpoints
        return $this->trackAssembly->toRepresentations($trackArray, $currentuserUid, $permissions);
    }

    public function getTrackByTrackUid(string $trackUid, string $currentuserUid): TrackRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        $track = $this->trackRepository->getTrackByUid($trackUid);


//        print_r($track);

        $isracePassed = $this->trackRepository->isRacePassed($trackUid);
        if ($isracePassed == true) {
            if ($this->settings['demo'] == 'true') {
                $track->setActive(true);
            } else {
                $track->setActive(false);
            }
        }
        return $this->trackAssembly->toRepresentation($track, $permissions, $currentuserUid);
    }

    public function updateTrack(TrackRepresentation $trackrepresentation, string $currentuserUid): ?TrackRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        if (!empty($trackrepresentation)) {
            $trackUpdated = $this->trackRepository->updateTrack($this->trackAssembly->totrack($trackrepresentation));
            return $this->trackAssembly->toRepresentation($trackUpdated, $permissions, $currentuserUid);
        }
        return null;

    }

    public function tracksForEvent(string $currentuserUid, string $event_uid): ?array
    {
        $permissions = $this->getPermissions($currentuserUid);
        $tracks = $this->trackRepository->tracksbyEvent($event_uid);

//        if (empty($track_uids)) {
//            return array();
//        }
//        $test = [];
//        foreach ($track_uids as $s => $ro) {
//            $test[] = $ro[$s];
//        }
//        $tracks = $this->trackRepository->tracksOnEvent($test);

        return $this->trackAssembly->toRepresentations($tracks, $currentuserUid, $permissions);
    }

    public function createTrack(TrackRepresentation $trackrepresentation, string $currentuserUid): TrackRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        $createdTrack = $this->trackRepository->createTrack($this->trackAssembly->totrack($trackrepresentation));
        return $this->trackAssembly->toRepresentation($createdTrack, $permissions, $currentuserUid);
    }

    public function createTrackWithOutCheckpoints(TrackRepresentation $trackrepresentation, string $currentuserUid): TrackRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        
        // Initialize all required properties with default values
        
        // Validate event_uid first
        $eventUid = $trackrepresentation->getEventUid();
        if (empty($eventUid)) {
            throw new BrevetException("Event UID is required for creating a track", 5, null);
        }
        
        // Validate event exists
        $event = $this->eventRepository->eventFor($eventUid);
        if (!$event) {
            throw new BrevetException("Event with UID {$eventUid} does not exist", 5, null);
        }
     
        // Create track and ensure track_uid is set
        $track = $this->trackAssembly->totrack($trackrepresentation);
    
     
        $track->setCheckpoints([]); // Ensure no checkpoints are set
        $createdTrack = $this->trackRepository->createTrack($track);
        
        if (!$createdTrack || !$createdTrack->getTrackUid()) {
            throw new BrevetException("Failed to create track - no track UID generated", 5, null);
        }
        
        // Create a new representation with the generated track_uid
        $representation = $this->trackAssembly->toRepresentation($createdTrack, $permissions, $currentuserUid);
        
        // Double check the representation has the track_uid
        if (!$representation->getTrackUid()) {
            throw new BrevetException("Track was created but representation is missing track UID", 5, null);
        }
        
        return $representation;
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("TRACK", $user_uid);
    }

    public function buildFromCsv(?string $filename, string $uploadDir, string $getAttribute)
    {
        // Lös in filen som laddades upp
        $csv = Reader::createFromPath($this->settings['upload_directory'] . $filename, 'r');
        $csv->setDelimiter(";");
        $csv->skipEmptyRecords();
        $csv->skipInputBOM();

        $records = $csv->getRecords();


        $stmt = Statement::create()
            ->offset(0);

        //query your records from the document
        $recordss = $stmt->process($csv)->getRecords();
        $recordsss = $stmt->process($csv)->getRecords();


        $eachEvent = new Dictionary();
        $eachEvent2 = new Dictionary();

        foreach ($records as $rowIndex => $record) {
            if (!$eachEvent2->hasValue($record[0])) {
                $eachEvent2 = $eachEvent2->withValue($record[0], new Dictionary());
            }
        }

        $rader = [];
        foreach ($recordss as $rowIndex => $record22) {
            array_push($rader, $record22);
        }


        foreach ($recordsss as $rowIndex => $record23) {
            $track = $eachEvent2->getValue($record23[0]);
            $dns = [];
            foreach ($rader as $indx => $ee) {
                if ($ee[1] . $ee[3] . $ee[4] == $record23[1] . $record23[3] . $record23[4]) {
                    array_push($dns, $ee);
                }
            }
            $track = $track->withValue($record23[1] . $record23[3] . $record23[4], $dns);
            $eachEvent2 = $eachEvent2->withValue($record23[0], $track);
        }


        $tracks = [];
        $eventTracksArray = [];
        $rader = [];
        foreach ($eachEvent2 as $key => $value) {
            $banaarr = [];
            $track = new TracksForEvents();
            $track->event = $this->createEvent($value);
            foreach ($value as $key2 => $value2) {
                $rader = [];
                $bana = new Bana();
                $bana->bana = $key2;
                if ($eachEvent2->hasValue($key)) {
                    $bana->bana = $key2;
                    foreach ($value2 as $key2 => $rade) {
                        array_push($rader, $rade);
                    }
                    $bana->controls = $rader;
                    array_push($banaarr, $bana);
                }
            }


            $track->tracks = $banaarr;
            array_push($eventTracksArray, $track);

        }

        foreach ($eventTracksArray as $key2 => $traclforevent) {

            foreach ($traclforevent->tracks as $key2 => $test) {
                $sites = $this->createSite($test->controls);
                $controlls = $this->createControl($test, $sites);
                $track->track = $this->buildTrackFromCsv($traclforevent, $test, $controlls);

            }
        }
    }


    private function createSite($record): ?array
    {

        $siteDict = new Dictionary();
        $sitereturn = [];
        foreach ($record as $key => $value) {
            if (!$siteDict->hasValue($value[5] . $value[6])) {

                $siteDict = $siteDict->withValue($value[5] . $value[6], new Site("", $value[5], $value[6], $value[13], null,
                    empty($value[8]) ? new DecimalNumber("0") : new DecimalNumber(strval($value[8])),
                    empty($value[9]) ? new DecimalNumber("0") : new DecimalNumber(strval($value[9])),
                    empty($value[15]) ? "" : $value[15]));

                $existingSite = $this->siteRepository->existsByPlaceAndAdress($value[5], $value[6]);

                if ($existingSite == null) {
                    array_push($sitereturn, $this->siteRepository->createSite($siteDict->getValue($value[5] . $value[6])));

                } else {

                    array_push($sitereturn, $existingSite);
                }
            }
        }
        return $sitereturn;


    }

    private function createEvent($record): ?Event
    {
        $events = "";
        $event = new Event();
        foreach ($record as $key => $value) {
            if ($events == "") {
                $event->setTitle($value[0][0]);
                $event->setActive(false);
                $event->setCanceled(false);
                $event->setCompleted(false);
                $event->setStartdate($value[0][3]);
                $event->setEnddate($value[0][4]);
                $existsing = $this->eventRepository->existsByTitleAndStartDate($value[0][0], $value[0][3]);
                if ($existsing == null) {
                    $event = $this->eventRepository->createEvent($event);
                } else {
                    $event = $existsing;
                }
            }
        }
        return $event;

    }

    private function createControl($rad, array $track): array
    {

        $checkpoints = [];
        foreach ($rad->controls as $key => $value) {

            $checkpoint = new Checkpoint();
            $sites = $track;
            foreach ($sites as $rowIndex => $record) {
                $place = $value[5];
                $adress = $value[6];

                if (strtolower($place) == strtolower($record->getPlace()) && strtolower($adress) == strtolower($record->getAdress())) {
                    $checkpoint->setSiteUid($record->getSiteUid());
                    $checkpoint->setOpens($value[11] != null ? $value[11] : null);
                    $checkpoint->setClosing($value[12] != null ? $value[12] : null);
                    $checkpoint->setDistance(floatval($value[10]));
                    $sss = $this->checkpointRepository->existsBySiteUidAndDistance($checkpoint->getSiteUid(), $checkpoint->getDistance(), $checkpoint->getOpens(), $checkpoint->getClosing());

                    if ($sss == null) {
                        $this->checkpointRepository->createCheckpoint(null, $checkpoint);
                        array_push($checkpoints, $checkpoint);
                    } else {
                        array_push($checkpoints, $sss);
                    }
                }
            }
        }

        return $checkpoints;
    }

    private function buildTrackFromCsv(TracksForEvents $trackin, $rad, array $controlls): ?Track
    {

        $trackout = null;

        foreach ($rad->controls as $key => $value) {
            $trackwithstartdate = $this->trackRepository->trackWithStartdateExists($trackin->event->getEventUid(), $value[1], $value[11]);
            if ($trackwithstartdate) {
                return $trackwithstartdate;
            }
            $checkpoints_uid = [];
            $trackToCreate = new Track();
            $trackToCreate->setDescription("");
            $trackToCreate->setTitle($value[1]);
            $trackToCreate->setLink($value[14]);
            $trackToCreate->setHeightdifference(2000);
            $trackToCreate->setDistance($value[2]);
            $trackToCreate->setStartDateTime($value[11]);
            $existintevent = $this->eventRepository->eventFor($trackin->event->getEventUid());
            if (isset($existintevent)) {
                $trackToCreate->setEventUid($trackin->event->getEventUid());
            }

            if (!empty($controlls)) {
                foreach ($controlls as $chp => $checkpoint) {
                    array_push($checkpoints_uid, $checkpoint->getCheckpointUid());
                }
                $trackToCreate->setCheckpoints($checkpoints_uid);
            }

            $trackwithstartdate = $this->trackRepository->trackWithStartdateExists($trackToCreate->getEventUid(), $trackToCreate->getTitle(), $value[10]);;
            if ($trackwithstartdate != null) {
                $trackout = $trackwithstartdate;
            }
            $trackout = $this->trackRepository->trackAndCheckpointsExists($trackToCreate->getEventUid(), $trackToCreate->getTitle(), $trackToCreate->getDistance(), $trackToCreate->getCheckpoints());
            if (!$trackout) {
                $trackout = $this->trackRepository->createTrack($trackToCreate);
            }

            break;
        }


        return $trackout;

    }

//    private function createTracksOnEvent($track, $event)
//    {
//        $track = '1e02e4f5-55e0-4211-9b17-23b28836463a';
//        $event = '36c89577-d041-49f8-82ba-b8eb1c6662ba';
//        $tracksOnEvent = $this->eventRepository->trackAndEventOnEvent($event, $track);
//
//
//        if(empty($tracksOnEvent)){
//            $this->eventRepository->createTrackEvent($event, $track);
//        }
//    }

    public function deleteTrack(?string $track_uid, string $currentuserUid)
    {
        $track = $this->trackRepository->getTrackByUid($track_uid);

        if ($track == null) {
            throw new BrevetException("Finns ingen bana med angivet uid", 5);
        }

        $participants = $this->participantRepository->getPArticipantsByTrackUids(array($track_uid));

        if (count($participants) > 0) {
            throw new BrevetException("Kan inte tabort banan. Det finns deltagare kopplade till banan", 5);
        }

        // Tabort checkpoints
        foreach ($track->getCheckpoints() as $checkpoint) {

            $checkpointa = $this->checkpointRepository->checkpointFor($checkpoint);
            if ($checkpointa != null) {
                $this->checkpointService->deleteCheckpoint($checkpoint);
            }

        }

        // Tabort kopplingen till banan
        $this->trackRepository->deleteTrackCheckpoint(array($track_uid));

        //Tabort själva banan
        $this->trackRepository->deleteTrack($track_uid);

    }

    public function publishResults(?string $track_uid, $publish, string $currentuserUid)
    {
        $track = $this->trackRepository->getTrackByUid($track_uid);
        if ($track == null) {
            throw new BrevetException("Finns ingen bana med angivet uid", 5);
        }

        if ($track->isActive()) {
            $this->trackRepository->setInactive($track_uid, 0);
        } else {
            $this->trackRepository->setInactive($track_uid, 1);
        }

    }

    public function planTrack(RusaPlannerInputRepresentation $rusaPlannnerInput, string $currentuserUid): object
    {
        try {
            // Check if the use_acp_calculator flag is set to true
            if ($rusaPlannnerInput->getUseAcpCalculator()) {
                return $this->rusaTimeTrackPlannerService->getResponseFromACPCalculator($rusaPlannnerInput, $currentuserUid);
            } else {
                // Use original RUSA Time calculation
                return $this->rusaTimeTrackPlannerService->getresponseFromRusaTime($rusaPlannnerInput, $currentuserUid);
            }
        } catch (\Exception $e) {
            // Log error and return empty response instead of letting the error bubble up
            error_log("Error in planTrack: " . $e->getMessage());
            return new \App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation();
        }
    }

    public function createTrackFromPlanner(stdClass $trackrepresentation, string $currentUserUId): TrackRepresentation
    {

        $event = $this->eventRepository->eventFor($trackrepresentation->eventRepresentation->event_uid);

        $trackToCreate = new Track();
        $trackToCreate->setEventUid($event->getEventUid());
        $trackToCreate->setDescription("");
        $trackToCreate->setTitle($trackrepresentation->rusaTrackRepresentation->TRACK_TITLE);
        $trackToCreate->setLink($trackrepresentation->rusaTrackRepresentation->LINK_TO_TRACK);
        $trackToCreate->setActive(true);
        $trackToCreate->setHeightdifference(2000);
        $trackToCreate->setDistance($trackrepresentation->rusaTrackRepresentation->EVENT_DISTANCE_KM);
        $trackToCreate->setStartDateTime($trackrepresentation->rusaTrackRepresentation->START_DATE . ' ' . $trackrepresentation->rusaTrackRepresentation->START_TIME);


        $checkpoints = array();
        foreach ($trackrepresentation->rusaplannercontrols as $checkpointiput) {
            $checkpoint = new Checkpoint();
            $checkpoint->setSiteUid($checkpointiput->siteRepresentation->site_uid);
            $checkpoint->setDistance($checkpointiput->rusaControlRepresentation->CONTROL_DISTANCE_KM);
            $open = date('Y-m-d H:i:s', strtotime($checkpointiput->rusaControlRepresentation->OPEN));
            $close = date('Y-m-d H:i:s', strtotime($checkpointiput->rusaControlRepresentation->CLOSE));
            $checkpoint->setOpens($open);
            $checkpoint->setClosing($close);
            array_push($checkpoints, $this->checkpointRepository->createCheckpoint(null, $checkpoint));
        }

        $checkUids = array();
        foreach ($checkpoints as $check){
            array_push($checkUids, $check->getCheckpointUid());
        }

        $trackToCreate->setCheckpoints($checkUids);
        $track = $this->trackRepository->createTrack($trackToCreate);

        return $this->trackAssembly->toRepresentation($track, array(), $currentUserUId);
    }


}
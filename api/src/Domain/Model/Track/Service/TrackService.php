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

        // If track is not found, throw an exception
        if ($track === null) {
            throw new BrevetException("Track not found with ID: " . $trackUid, 404);
        }

        $isracePassed = $this->trackRepository->isRacePassed($trackUid);
        
        error_log("Final active status: " . var_export($track->isActive(), true));
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

        // Store checkpoints for later deletion
        $checkpointsToDelete = [];
        foreach ($track->getCheckpoints() as $checkpoint) {
            $checkpointa = $this->checkpointRepository->checkpointFor($checkpoint);
            if ($checkpointa != null) {
                $checkpointsToDelete[] = $checkpoint;
            }
        }

        // First remove the track-checkpoint associations
        $this->trackRepository->deleteTrackCheckpoint(array($track_uid));

        // Then delete the checkpoints
        foreach ($checkpointsToDelete as $checkpoint) {
            $this->checkpointService->deleteCheckpoint($checkpoint);
        }

        // Finally delete the track itself
        $this->trackRepository->deleteTrack($track_uid);

        // Try to delete corresponding event in loppservice if it exists
        try {
            $loppServiceClient = new \App\common\Rest\Client\LoppServiceEventRestClient($this->settings);
            
            // Directly try to delete the event - if it doesn't exist, the API will return 404
            $deleteSuccess = $loppServiceClient->deleteEvent($track_uid);
            
            if ($deleteSuccess) {
                error_log("Successfully deleted event from loppservice: " . $track_uid);
            } else {
                // Check if the event exists first to determine if the failure is due to "not found"
                $existingEvent = $loppServiceClient->getEventById($track_uid);
                if ($existingEvent === null) {
                    error_log("Event not found in loppservice (this is OK): " . $track_uid);
                } else {
                    error_log("Failed to delete event from loppservice (event exists but deletion failed): " . $track_uid);
                }
            }
        } catch (\Exception $e) {
            // Log the error but don't fail the main deletion
            error_log("Error deleting event in loppservice: " . $e->getMessage());
            error_log("Track deletion completed locally, but loppservice sync failed for track: " . $track_uid);
        }
    }

    public function publishResults(?string $track_uid, $publish, string $currentuserUid)
    {
        error_log("publishResults called with track_uid: $track_uid, publish: " . var_export($publish, true) . ", user: $currentuserUid");
        
        $track = $this->trackRepository->getTrackByUid($track_uid);
        if ($track == null) {
            throw new BrevetException("Finns ingen bana med angivet uid", 5);
        }

        error_log("Track found - current active status: " . var_export($track->isActive(), true));

        // Get the database connection from the repository for transaction handling
        $connection = $this->trackRepository->getConnection();
        
        // Begin transaction
        $connection->beginTransaction();
        
        try {
            // When publishing (publish=true), set active=0 (published)
            // When unpublishing (publish=false), set active=1 (unpublished)
            $newActiveState = ($publish === true || $publish === "true") ? 0 : 1;
            error_log("Setting track active state to: " . $newActiveState);
            
            $this->trackRepository->setInactive($track_uid, $newActiveState);

            // Commit the transaction
            $connection->commit();
            error_log("Transaction committed successfully");
            
            // Return the updated track representation
            $permissions = $this->getPermissions($currentuserUid);
            return $this->trackAssembly->toRepresentation($this->trackRepository->getTrackByUid($track_uid), $permissions, $currentuserUid);
            
        } catch (\Exception $e) {
            // Rollback the transaction on any error
            $connection->rollBack();
            error_log("Transaction rolled back due to error: " . $e->getMessage());
            
            if ($e instanceof BrevetException) {
                throw $e;
            }
            
            throw new BrevetException("Ett fel uppstod vid uppdatering av banan", 6, $e);
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

    public function createTrackFromPlanner(stdClass $trackrepresentation, string $currentUserUId, $formData = null): TrackRepresentation
    {


        // Validate that a track with the same name and date/time doesn't already exist
        $startDateTime = $trackrepresentation->rusaTrackRepresentation->START_DATE . ' ' . $trackrepresentation->rusaTrackRepresentation->START_TIME;
        $existingTrack = $this->trackRepository->getTrackByTitleAndDate(
            $trackrepresentation->rusaTrackRepresentation->TRACK_TITLE,
            $startDateTime
        );

        if ($existingTrack !== null) {
            throw new BrevetException(  
                "A track with the title '{$trackrepresentation->rusaTrackRepresentation->TRACK_TITLE}' and date '{$trackrepresentation->rusaTrackRepresentation->START_DATE}' already exists.", 
                9, 
                null
            );
        }
        try {
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
            
            // Set organizer_id from form data if available
            if ($formData && isset($formData->organizer_id)) {
                $trackToCreate->setOrganizerId($formData->organizer_id);
            }

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

            // If form data is provided, create event in loppservice
            if ($formData) {
                $loppserviceSuccess = $this->createEventInLoppservice($trackrepresentation, $formData, $event, $track->getTrackUid());
                if (!$loppserviceSuccess) {
                    // Cleanup: delete the created track and checkpoints
                    $this->deleteTrack($track->getTrackUid(), $currentUserUId);
                    throw new BrevetException("Failed to create event in event calender. Track creation cancelled.", 9, null);
                }
            }

            return $this->trackAssembly->toRepresentation($track, array(), $currentUserUId);
            
        } catch (\Exception $e) {
            throw $e;
        }
    }

    public function getTrackByUid(string $trackUid): ?Track
    {
        return $this->trackRepository->getTrackByUid($trackUid);
    }

    public function updateTrackAndEvent(TrackRepresentation $trackrepresentation, string $currentuserUid, $formData = null, $checkpointData = null): TrackRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        
        // Get the existing track to preserve important data
        $existingTrack = $this->trackRepository->getTrackByUid($trackrepresentation->getTrackUid());
        if (!$existingTrack) {
            throw new BrevetException("Track not found with UID: " . $trackrepresentation->getTrackUid(), 404);
        }
        
        // Handle checkpoints if provided
        if ($checkpointData && isset($checkpointData->rusaplannercontrols)) {
            // Check if there are participants on this track
            $participants = $this->participantRepository->participantsOnTrack($existingTrack->getTrackUid());
            $hasParticipants = !empty($participants);
            
            if ($hasParticipants) {
                // Check if any participants have started (have stamps on checkpoints)
                $hasStartedParticipants = false;
                foreach ($participants as $participant) {
                    if ($participant->isStarted() || $participant->isFinished() || $participant->isDnf() || $participant->isDns()) {
                        $hasStartedParticipants = true;
                        break;
                    }
                }
                
                if ($hasStartedParticipants) {
                    throw new BrevetException("Cannot update checkpoints for track with participants who have already started. Please remove participants first.", 9, null);
                }
            }
            
            // Delete existing checkpoints first
            $this->deleteExistingCheckpoints($existingTrack);
            
            // Create new checkpoints
            $checkpoints = array();
            foreach ($checkpointData->rusaplannercontrols as $checkpointInput) {
                $checkpoint = new Checkpoint();
                $checkpoint->setSiteUid($checkpointInput->siteRepresentation->site_uid);
                $checkpoint->setDistance($checkpointInput->rusaControlRepresentation->CONTROL_DISTANCE_KM);
                $open = date('Y-m-d H:i:s', strtotime($checkpointInput->rusaControlRepresentation->OPEN));
                $close = date('Y-m-d H:i:s', strtotime($checkpointInput->rusaControlRepresentation->CLOSE));
                $checkpoint->setOpens($open);
                $checkpoint->setClosing($close);
                array_push($checkpoints, $this->checkpointRepository->createCheckpoint(null, $checkpoint));
            }

            $checkUids = array();
            foreach ($checkpoints as $check) {
                array_push($checkUids, $check->getCheckpointUid());
            }
            
            // Set the new checkpoints in the track representation
            // Convert to the format expected by TrackAssembly
            $checkpointArray = array();
            foreach ($checkUids as $uid) {
                $checkpointArray[] = array('checkpoint_uid' => $uid);
            }
            $trackrepresentation->setCheckpoints($checkpointArray);
        }
        
        // Update the track locally
        $updatedTrack = $this->trackRepository->updateTrack($this->trackAssembly->totrack($trackrepresentation));
        
        // If checkpoints were updated, we need to manually create the track-checkpoint associations
        if ($checkpointData && isset($checkpointData->rusaplannercontrols)) {
            $this->createTrackCheckpointAssociations($updatedTrack->getTrackUid(), $trackrepresentation->getCheckpoints());
            
            // Update participant checkpoints if participants exist
            $participants = $this->participantRepository->participantsOnTrack($updatedTrack->getTrackUid());
            if (!empty($participants)) {
                $this->updateParticipantCheckpoints($updatedTrack->getTrackUid(), $checkUids);
            }
        }
        
        // If form data is provided, update the corresponding event in loppservice
        if ($formData) {
            $this->updateEventInLoppservice($trackrepresentation, $formData, $updatedTrack);
        }
        
        // Get the updated track with checkpoints loaded
        $finalTrack = $this->trackRepository->getTrackByUid($updatedTrack->getTrackUid());
        
        return $this->trackAssembly->toRepresentation($finalTrack, $permissions, $currentuserUid);
    }

    private function deleteExistingCheckpoints(Track $track): void
    {
        // Store checkpoints for later deletion
        $checkpointsToDelete = [];
        foreach ($track->getCheckpoints() as $checkpoint) {
            $checkpointObj = $this->checkpointRepository->checkpointFor($checkpoint);
            if ($checkpointObj != null) {
                $checkpointsToDelete[] = $checkpoint;
            }
        }

        // First remove the track-checkpoint associations
        $this->trackRepository->deleteTrackCheckpoint(array($track->getTrackUid()));

        // Then delete the checkpoints
        foreach ($checkpointsToDelete as $checkpoint) {
            $this->checkpointService->deleteCheckpoint($checkpoint);
        }
    }

    private function createTrackCheckpointAssociations(string $trackUid, array $checkpoints): void
    {
        // Create track-checkpoint associations in the database
        $query = $this->trackRepository->getConnection()->prepare("INSERT INTO track_checkpoint(track_uid, checkpoint_uid) VALUES (:track_uid, :checkpoint_uid)");
        
        foreach ($checkpoints as $checkpoint) {
            if (isset($checkpoint['checkpoint_uid'])) {
                $query->bindParam(':track_uid', $trackUid);
                $query->bindParam(':checkpoint_uid', $checkpoint['checkpoint_uid']);
                $query->execute();
            }
        }
    }

    private function createEventInLoppservice(stdClass $trackrepresentation, $formData, $event, $trackUid = null): bool
    {
        try {
            // Import the LoppService Event REST Client
            $loppServiceClient = new \App\common\Rest\Client\LoppServiceEventRestClient($this->settings);

            // Create EventDTO object instead of stdClass
            $eventDTO = new \App\common\Rest\DTO\EventDTO();
            
            // Set event title as-is (no UID or timestamp appended)
            $eventDTO->title = $formData->trackname ?? $trackrepresentation->rusaTrackRepresentation->TRACK_TITLE;
            
            $eventDTO->description = $formData->description ?? '';
            $eventDTO->startdate = $formData->startdate ?? $trackrepresentation->rusaTrackRepresentation->START_DATE;
            $eventDTO->enddate = $formData->startdate ?? $trackrepresentation->rusaTrackRepresentation->START_DATE; // Same as start date for brevets
            $eventDTO->event_type = $formData->event_type ?? 'BRM'; // Use form data or default to BRM
            $eventDTO->organizer_id = $formData->organizer_id ?? null;
            
            // Set event_uid to track_uid to link the systems
            if ($trackUid) {
                $eventDTO->event_uid = $trackUid;
            }
        

            // Create route details DTO
            $routeDetailsDTO = new \App\common\Rest\DTO\RouteDetailsDTO();
            $routeDetailsDTO->distance = $trackrepresentation->rusaTrackRepresentation->EVENT_DISTANCE_KM;
            $routeDetailsDTO->start_time = $formData->starttime ?? $trackrepresentation->rusaTrackRepresentation->START_TIME;
            $routeDetailsDTO->name = $formData->trackname ?? $trackrepresentation->rusaTrackRepresentation->TRACK_TITLE;
            $routeDetailsDTO->description = $formData->description ?? '';
            $routeDetailsDTO->track_link = $formData->link ?? $trackrepresentation->rusaTrackRepresentation->LINK_TO_TRACK;
            $routeDetailsDTO->start_place = $formData->startLocation ?? '';
            $routeDetailsDTO->height_difference = $formData->elevation ?? 0;
            
            $eventDTO->route_detail = $routeDetailsDTO;

            // Create event configuration DTO
            $eventConfigDTO = new \App\common\Rest\DTO\EventConfigurationDTO();
            $eventConfigDTO->use_stripe_payment = $formData->stripe_payment ?? false;
            $eventConfigDTO->max_registrations = $formData->max_participants ?? 300; // Use form data or default

            // Set registration dates - always provide defaults if not set
            if (isset($formData->registration_opens)) {
                $eventConfigDTO->registration_opens = $formData->registration_opens;
            } else {
                // Default: 30 days before event start
                $eventStartDate = new \DateTime($formData->startdate ?? $trackrepresentation->rusaTrackRepresentation->START_DATE);
                $eventStartDate->sub(new \DateInterval('P30D'));
                $eventConfigDTO->registration_opens = $eventStartDate->format('Y-m-d') . ' 00:00:00';
            }
            
            if (isset($formData->registration_closes)) {
                $eventConfigDTO->registration_closes = $formData->registration_closes;
            } else {
                // Default: 23:59 the day before event starts
                $eventStartDate = new \DateTime($formData->startdate ?? $trackrepresentation->rusaTrackRepresentation->START_DATE);
                $eventStartDate->sub(new \DateInterval('P1D'));
                $eventConfigDTO->registration_closes = $eventStartDate->format('Y-m-d') . ' 23:59:00';
            }

            // Create start number configuration DTO
            $startNumberConfigDTO = new \App\common\Rest\DTO\StartNumberConfigDTO();
            $beginsAt = $formData->startnumber_begins_at ?? 1001;
            $maxParticipants = $formData->max_participants ?? 300;
            $increments = 1; // Fixed increment value
            
            $startNumberConfigDTO->begins_at = $beginsAt;
            $startNumberConfigDTO->ends_at = $beginsAt + $maxParticipants - 1; // Calculate ends_at from max participants
            $startNumberConfigDTO->increments = $increments;

            $eventConfigDTO->startnumberconfig = $startNumberConfigDTO;
            $eventDTO->eventconfiguration = $eventConfigDTO;

            // Create event in loppservice
            try {
                $createdEvent = $loppServiceClient->createEvent($eventDTO);
                
                if ($createdEvent !== null) {
                    return true;
                } else {
                    error_log("Failed to create event in loppservice");
                    return false;
                }
            } catch (\Exception $e) {
                // Check if it's an "alreadyexists" error
                if ($e->getMessage() === 'alreadyexists') {
                    error_log("Event with this title already exists in loppservice");
                    throw new BrevetException("Ett event med detta namn finns redan i loppservice. Vänligen välj ett annat namn.", 9);
                }
                
                // Re-throw other exceptions
                throw $e;
            }
            
        } catch (\Exception $e) {
            error_log("Exception in createEventInLoppservice: " . $e->getMessage());
            error_log("Loppservice URL being used: " . $this->settings['loppserviceurl']);
            
            // Re-throw the exception to let the calling code handle it
            throw $e;
        }
    }

    private function updateEventInLoppservice(TrackRepresentation $trackrepresentation, $formData, $track): bool
    {
        try {
            // Import the LoppService Event REST Client
            $loppServiceClient = new \App\common\Rest\Client\LoppServiceEventRestClient($this->settings);

            // Create EventDTO object
            $eventDTO = new \App\common\Rest\DTO\EventDTO();
            
            // Use track data for basic event info
            $eventDTO->title = $formData->trackname ?? $trackrepresentation->getTitle();
            $eventDTO->description = $formData->description ?? '';
            $eventDTO->startdate = $formData->startdate ?? date('Y-m-d', strtotime($trackrepresentation->getStartDateTime()));
            $eventDTO->enddate = $formData->startdate ?? date('Y-m-d', strtotime($trackrepresentation->getStartDateTime()));
            $eventDTO->event_type = $formData->event_type ?? 'BRM';
            $eventDTO->organizer_id = $formData->organizer_id ?? null;
            
            // Set event_uid to track_uid to link the systems
            $eventDTO->event_uid = $track->getTrackUid();

            // Create route details DTO
            $routeDetailsDTO = new \App\common\Rest\DTO\RouteDetailsDTO();
            $routeDetailsDTO->distance = $trackrepresentation->getDistance();
            $routeDetailsDTO->start_time = $formData->starttime ?? date('H:i', strtotime($trackrepresentation->getStartDateTime()));
            $routeDetailsDTO->name = $formData->trackname ?? $trackrepresentation->getTitle();
            $routeDetailsDTO->description = $formData->description ?? '';
            $routeDetailsDTO->track_link = $formData->link ?? $trackrepresentation->getLink();
            $routeDetailsDTO->start_place = $formData->startLocation ?? '';
            $routeDetailsDTO->height_difference = $formData->elevation ?? $trackrepresentation->getHeightdifference();
            
            $eventDTO->route_detail = $routeDetailsDTO;

            // Create event configuration DTO
            $eventConfigDTO = new \App\common\Rest\DTO\EventConfigurationDTO();
            $eventConfigDTO->use_stripe_payment = $formData->stripe_payment ?? false;
            $eventConfigDTO->max_registrations = $formData->max_participants ?? 300;

            // Set registration dates - always provide defaults if not set
            if (isset($formData->registration_opens)) {
                $eventConfigDTO->registration_opens = $formData->registration_opens;
            } else {
                // Default: 30 days before event start
                $eventStartDate = new \DateTime(date('Y-m-d', strtotime($trackrepresentation->getStartDateTime())));
                $eventStartDate->sub(new \DateInterval('P30D'));
                $eventConfigDTO->registration_opens = $eventStartDate->format('Y-m-d') . ' 00:00:00';
            }
            
            if (isset($formData->registration_closes)) {
                $eventConfigDTO->registration_closes = $formData->registration_closes;
            } else {
                // Default: 23:59 the day before event starts
                $eventStartDate = new \DateTime(date('Y-m-d', strtotime($trackrepresentation->getStartDateTime())));
                $eventStartDate->sub(new \DateInterval('P1D'));
                $eventConfigDTO->registration_closes = $eventStartDate->format('Y-m-d') . ' 23:59:00';
            }

            // Create start number configuration DTO
            $startNumberConfigDTO = new \App\common\Rest\DTO\StartNumberConfigDTO();
            $beginsAt = $formData->startnumber_begins_at ?? 1001;
            $maxParticipants = $formData->max_participants ?? 300;
            $increments = 1; // Fixed increment value
            
            $startNumberConfigDTO->begins_at = $beginsAt;
            $startNumberConfigDTO->ends_at = $beginsAt + $maxParticipants - 1;
            $startNumberConfigDTO->increments = $increments;

            $eventConfigDTO->startnumberconfig = $startNumberConfigDTO;
            $eventDTO->eventconfiguration = $eventConfigDTO;

            // Update event in loppservice
            try {
                $updatedEvent = $loppServiceClient->updateEvent($track->getTrackUid(), $eventDTO);
                
                if ($updatedEvent !== null) {
                    return true;
                } else {
                    error_log("Failed to update event in loppservice");
                    return false;
                }
            } catch (\Exception $e) {
                error_log("Error updating event in loppservice: " . $e->getMessage());
                // Don't throw exception for loppservice errors, just log and continue
                return false;
            }
            
        } catch (\Exception $e) {
            error_log("Exception in updateEventInLoppservice: " . $e->getMessage());
            error_log("Loppservice URL being used: " . $this->settings['loppserviceurl']);
            
            // Don't throw exception for loppservice errors, just log and continue
            return false;
        }
    }

    private function updateParticipantCheckpoints(string $trackUid, array $newCheckpointUids): void
    {
        // Get all participants on this track
        $participants = $this->participantRepository->participantsOnTrack($trackUid);
        
        foreach ($participants as $participant) {
            // Delete existing participant checkpoints for this participant
            $this->participantRepository->deleteParticipantCheckpointOnTrackByParticipantUid($participant->getParticipantUid());
            
            // Create new participant checkpoints for this participant
            $this->participantRepository->createTrackCheckpointsFor($participant, $newCheckpointUids);
        }
    }

}
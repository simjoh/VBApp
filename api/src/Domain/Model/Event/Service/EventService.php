<?php

namespace App\Domain\Model\Event\Service;

use App\common\Exceptions\BrevetException;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Event\Event;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Event\Rest\EventAssembly;
use App\Domain\Model\Event\Rest\EventInformationAssembly;
use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Model\Partisipant\Service\ParticipantService;
use App\Domain\Model\Stats\Repository\StatisticsRepository;
use App\Domain\Model\Track\Rest\TrackInformationAssembly;
use App\Domain\Model\Track\Service\TrackService;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;
use App\common\Rest\Client\LoppServiceEventGroupRestClient;
use App\common\Rest\DTO\EventGroupDTO;
use Ramsey\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Response;
use Fig\Http\Message\StatusCodeInterface;
use PDO;
use PDOException;

class EventService extends ServiceAbstract
{

    private $eventRepository;
    private $permissinrepository;
    private $eventAssembly;
    private $eventInformationAssembly;
    private $trackservice;
    private $checkpointsService;
    private $participantService;
    private $trackInformationAssembly;
    private $statisticsRepository;
    private $eventGroupRestClient;


    public function __construct(ContainerInterface       $c, EventRepository $eventRepository,
                                PermissionRepository     $permissionRepository,
                                EventAssembly            $eventAssembly,
                                EventInformationAssembly $eventInformationAssembly,
                                TrackService             $trackService,
                                CheckpointsService       $checkpointsService, ParticipantService $participantService,
                                TrackInformationAssembly $trackInformationAssembly, StatisticsRepository $statisticsRepository)
    {
    
        $this->eventRepository = $eventRepository;
        $this->permissinrepository = $permissionRepository;
        $this->eventAssembly = $eventAssembly;
        $this->eventInformationAssembly = $eventInformationAssembly;
        $this->trackservice = $trackService;
        $this->checkpointsService = $checkpointsService;
        $this->participantService = $participantService;
        $this->trackInformationAssembly = $trackInformationAssembly;
        $this->statisticsRepository = $statisticsRepository;
        

        
        // Initialize the event group rest client
        $settings = $c->get('settings');
        $this->eventGroupRestClient = new LoppServiceEventGroupRestClient($settings);
    }

    public function allEvents(string $currentUserUid): array
    {
        $events = $this->eventRepository->allEvents();
        if (!isset($events)) {
            return array();
        }

        return $this->eventAssembly->toRepresentations($events, $currentUserUid);
    }

    public function eventFor(string $event_uid, string $currentUserUid)
    {

        $event = $this->eventRepository->eventFor($event_uid);

        if (!isset($event)) {
            return null;
        }
        $permissions = $this->getPermissions($currentUserUid);
        return $this->eventAssembly->toRepresentation($event, $permissions);
    }

    public function updateEvent(string $event_uid, EventRepresentation $eventRepresentation, string $currentUserUid): EventRepresentation
    {
        $event = $this->toEvent($eventRepresentation);
        $permissions = $this->getPermissions($currentUserUid);

        $eventexists = $this->eventRepository->eventFor($event_uid);

        if(!$eventexists){
            throw new BrevetException("Event not found", 1, null);
        }
    
        // Get the database connection from the repository
        $connection = $this->eventRepository->getConnection();
        
        // Begin transaction
        $connection->beginTransaction();
        
        try {
            // Update event in local database first
            $event = $this->eventRepository->updateEvent($event_uid, $event);
            
            // Try to sync with LoppService, but don't fail if LoppService is unavailable
            try {
                // Try to get the event group by event UID
                $eventGroup = $this->eventGroupRestClient->getEventGroupById($event->getEventUid());
        
                if ($eventGroup) {
                    // Update the existing event group
                    $eventGroupDTO = $this->updateEventGroupFromEvent($eventGroup, $event);
                    
                    $updatedEventGroup = $this->eventGroupRestClient->updateEventGroup($event->getEventUid(), $eventGroupDTO);
                    
                    if (!$updatedEventGroup) {
                        error_log("Warning: Failed to update event group in LoppService, but continuing with local update: " . $event->getEventUid());
                    } else {
                        error_log("Successfully updated event group in LoppService: " . $event->getEventUid());
                    }
                } else {
                    // Event group doesn't exist in LoppService, try to create it
                    $eventGroupDTO = $this->createEventGroupFromEvent($event);
                    
                    $createdEventGroup = $this->eventGroupRestClient->createEventGroup($eventGroupDTO);
                    
                    if (!$createdEventGroup) {
                        error_log("Warning: Failed to create event group in LoppService, but continuing with local update: " . $event->getEventUid());
                    } else {
                        error_log("Successfully created event group in LoppService: " . $event->getEventUid());
                    }
                }
            } catch (\Exception $loppServiceException) {
                // Only skip LoppService for connection errors, otherwise re-throw
                $errorMessage = $loppServiceException->getMessage();
                if (strpos($errorMessage, 'Could not resolve host') !== false || 
                    strpos($errorMessage, 'Connection refused') !== false ||
                    strpos($errorMessage, 'Connection timed out') !== false) {
                    // Connection issue - log and continue
                    error_log("LoppService connection failed for event " . $event->getEventUid() . ": " . $errorMessage);
                    error_log("Continuing with local event update only");
                } else {
                    // Other error - re-throw to maintain existing behavior for non-connection issues
                    throw $loppServiceException;
                }
            }
            
            $connection->commit();
            return $this->eventAssembly->toRepresentation($event, $permissions);
            
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            $connection->rollBack();
            
            // Log the error with more details
            error_log("Failed to update event with rollback: " . $e->getMessage());
            error_log("Event data: " . json_encode([
                'uid' => $event->getEventUid(),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'startdate' => $event->getStartdate(),
                'enddate' => $event->getEnddate()
            ]));
            
            // Re-throw the exception to be handled by the caller
            throw new BrevetException("Det gick inte att uppdatera event: " . $e->getMessage(), 14, $e);
        }
    }

    public function createEvent(EventRepresentation $eventRepresentation, string $currentUserUid)
    {
        $event = $this->toEvent($eventRepresentation);
        
        // Always generate UUID in API first
        $event->setEventUid((string) Uuid::uuid4());
        
        $permissions = $this->getPermissions($currentUserUid);
        
        // Get the database connection from the repository
        $connection = $this->eventRepository->getConnection();
        
        // Begin transaction
        $connection->beginTransaction();
        
        try {
            // Create event in local database first
            $event = $this->eventRepository->createEvent($event);

            // Try to create corresponding event group in LoppService, but don't fail if unavailable
            try {
                $eventGroupDTO = $this->createEventGroupFromEvent($event);
                
                // Debug log
                error_log("Creating event group with data: " . json_encode([
                    'uid' => $eventGroupDTO->uid,
                    'name' => $eventGroupDTO->name,
                    'description' => $eventGroupDTO->description,
                    'startdate' => $eventGroupDTO->startdate,
                    'enddate' => $eventGroupDTO->enddate
                ]));
                
                $createdEventGroup = $this->eventGroupRestClient->createEventGroup($eventGroupDTO);
        
                if (!$createdEventGroup) {
                    error_log("Warning: Failed to create event group in LoppService, but continuing with local event: " . $event->getEventUid());
                } else {
                    error_log("Successfully created event group in LoppService: " . $event->getEventUid());
                }
            } catch (\Exception $loppServiceException) {
                // Only skip LoppService for connection errors, otherwise re-throw
                $errorMessage = $loppServiceException->getMessage();
                if (strpos($errorMessage, 'Could not resolve host') !== false || 
                    strpos($errorMessage, 'Connection refused') !== false ||
                    strpos($errorMessage, 'Connection timed out') !== false) {
                    // Connection issue - log and continue
                    error_log("LoppService connection failed for new event " . $event->getEventUid() . ": " . $errorMessage);
                    error_log("Continuing with local event creation only");
                } else {
                    // Other error - re-throw to maintain existing behavior for non-connection issues
                    throw $loppServiceException;
                }
            }
            
            $connection->commit();
            return $this->eventAssembly->toRepresentation($event, $permissions);
        } catch (\Exception $e) {
            // Rollback the transaction if any exception occurs
            $connection->rollBack();
            print_r($e->getMessage());
            // Log the error with more details
            error_log("Failed to create event with rollback. Error: " . $e->getMessage());
            error_log("Event data: " . json_encode([
                'uid' => $event->getEventUid(),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'startdate' => $event->getStartdate(),
                'enddate' => $event->getEnddate()
            ]));
            
            // Re-throw the exception to be handled by the caller
            throw new BrevetException("Det gick inte att skapa event: " . $e->getMessage(), 11, $e);
        }
    }

    public function deleteEvent(string $event_uid, string $currentUserUid)
    {
        $tracks = $this->trackservice->tracksForEvent($currentUserUid, $event_uid);

        if (!empty($tracks)) {
            throw new BrevetException("Det finns banor kopplade till eventet. Banorna måste tas bort från eventet", 5, null);
        }

        // Get the database connection from the repository
        $connection = $this->eventRepository->getConnection();
        
        try {
            $connection->beginTransaction();

            // Delete the event locally first
            $this->eventRepository->deleteEvent($event_uid);
            
            // Try to delete in LoppService
            try {
                $this->eventGroupRestClient->deleteEventGroup($event_uid);
                $connection->commit();
            } catch (\Exception $e) {
                // Check if the error message indicates the group wasn't found
                if (strpos($e->getMessage(), 'Event group not found') !== false) {
                    error_log("Event group not found in LoppService (this is OK): " . $e->getMessage());
                    $connection->commit();
                    return;
                }
                
                // For any other error, rollback and throw
                $connection->rollBack();
                throw new BrevetException("Det gick inte att ta bort eventgrupp i loppservice: " . $e->getMessage(), 15, $e);
            }
        } catch (\Exception $e) {
            if ($connection->inTransaction()) {
                $connection->rollBack();
            }
            throw new BrevetException("Det gick inte att ta bort event: " . $e->getMessage(), 16, $e);
        }
    }

    private function toEvent(EventRepresentation $eventRepresentation): Event
    {
        $event = new Event();
        $event->setEventUid($eventRepresentation->getEventUid());
        $event->setDescription(is_null($eventRepresentation->getDescription()) ? "" : $eventRepresentation->getDescription());
        $event->setTitle($eventRepresentation->getTitle());
        $event->setActive($eventRepresentation->isActive());
        $event->setCanceled($eventRepresentation->isCanceled());
        $event->setCompleted($eventRepresentation->isCompleted());
        
        // Format dates properly for MySQL storage
        $event->setStartdate($this->formatDateForDatabase($eventRepresentation->getStartdate()));
        $event->setEnddate($this->formatDateForDatabase($eventRepresentation->getEnddate()));

        return $event;
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("EVENT", $user_uid);

    }

    public function eventInformation(string $eventUid, string $currentUserUid): array
    {
        $startTime = microtime(true);
        error_log("EventService::eventInformation START - eventUid: $eventUid");
        
        $permissions = $this->getPermissions($currentUserUid);
        $eventInfos = array();

        if ($eventUid != "") {
            // Single event optimization
            $eventStart = microtime(true);
            $event = $this->eventRepository->eventFor($eventUid);
            $eventTime = microtime(true) - $eventStart;
            error_log("EventService::eventInformation - Single event fetch took: " . number_format($eventTime * 1000, 2) . "ms");
            
            if (!$event) {
                error_log("EventService::eventInformation END - No event found, total time: " . number_format((microtime(true) - $startTime) * 1000, 2) . "ms");
                return [];
            }
            
            $optimizedStart = microtime(true);
            $eventInfos = $this->getEventInformationOptimized([$event], $currentUserUid, $permissions);
            $optimizedTime = microtime(true) - $optimizedStart;
            error_log("EventService::eventInformation - getEventInformationOptimized took: " . number_format($optimizedTime * 1000, 2) . "ms");
        } else {
            // Multiple events optimization
            $eventsStart = microtime(true);
            $events = $this->eventRepository->allEvents();
            $eventsTime = microtime(true) - $eventsStart;
            error_log("EventService::eventInformation - allEvents fetch took: " . number_format($eventsTime * 1000, 2) . "ms");
            
            $optimizedStart = microtime(true);
            $eventInfos = $this->getEventInformationOptimized($events, $currentUserUid, $permissions);
            $optimizedTime = microtime(true) - $optimizedStart;
            error_log("EventService::eventInformation - getEventInformationOptimized took: " . number_format($optimizedTime * 1000, 2) . "ms");
        }

        // Sort eventInfos by startdate (desc) and then by title
        $sortStart = microtime(true);
        usort($eventInfos, function($a, $b) {
            $dateCompare = strtotime($b->getEvent()->getStartdate()) - strtotime($a->getEvent()->getStartdate());
            if ($dateCompare === 0) {
                return strcmp($b->getEvent()->getTitle(), $a->getEvent()->getTitle());
            }
            return $dateCompare;
        });
        $sortTime = microtime(true) - $sortStart;
        error_log("EventService::eventInformation - Sorting took: " . number_format($sortTime * 1000, 2) . "ms");

        $totalTime = microtime(true) - $startTime;
        error_log("EventService::eventInformation END - Total time: " . number_format($totalTime * 1000, 2) . "ms, returned " . count($eventInfos) . " events");

        return $eventInfos;
    }

    /**
     * Optimized method to get event information with batched database queries
     * 
     * @param array $events Array of Event objects
     * @param string $currentUserUid Current user UID
     * @param array $permissions User permissions
     * @return array Array of EventInformationRepresentation objects
     */
    private function getEventInformationOptimized(array $events, string $currentUserUid, array $permissions): array
    {
        $startTime = microtime(true);
        error_log("EventService::getEventInformationOptimized START - processing " . count($events) . " events");
        
        if (empty($events)) {
            return [];
        }

        $eventInfos = [];
        $allTrackUids = [];
        $eventTrackMap = [];

        // Step 1: Collect all event UIDs and batch fetch tracks for all events
        $step1Start = microtime(true);
        $eventUids = [];
        foreach ($events as $event) {
            $eventUids[] = $event->getEventUid();
        }
        
        // Batch fetch all tracks for all events in one query
        $allTracks = $this->getTracksForEventsBatch($eventUids, $currentUserUid);
        error_log("EventService::getEventInformationOptimized - Batch fetched " . count($allTracks) . " tracks for " . count($eventUids) . " events");
        
        // Group tracks by event
        $tracksByEvent = [];
        foreach ($allTracks as $track) {
            $eventUid = $track->getEventUid();
            if (!isset($tracksByEvent[$eventUid])) {
                $tracksByEvent[$eventUid] = [];
            }
            $tracksByEvent[$eventUid][] = $track;
        }
        
        // Build event track map
        foreach ($events as $event) {
            $eventUid = $event->getEventUid();
            $tracks = $tracksByEvent[$eventUid] ?? [];
            
            $trackUids = [];
            foreach ($tracks as $track) {
                $trackUids[] = $track->getTrackUid();
                $allTrackUids[] = $track->getTrackUid();
            }
            
            $eventTrackMap[$eventUid] = [
                'event' => $event,
                'tracks' => $tracks,
                'trackUids' => $trackUids
            ];
        }
        $step1Time = microtime(true) - $step1Start;
        error_log("EventService::getEventInformationOptimized - Step 1 (batch collect tracks) took: " . number_format($step1Time * 1000, 2) . "ms, found " . count($allTrackUids) . " tracks");

        // Step 2: Batch fetch all track statistics in one query
        $trackStatsMap = [];
        if (!empty($allTrackUids)) {
            $step2Start = microtime(true);
            $trackStatsMap = $this->getTrackStatisticsBatch($allTrackUids);
            $step2Time = microtime(true) - $step2Start;
            error_log("EventService::getEventInformationOptimized - Step 2 (batch track statistics) took: " . number_format($step2Time * 1000, 2) . "ms");
        }

        // Step 3: Batch fetch all track details in one query
        $trackDetailsMap = [];
        if (!empty($allTrackUids)) {
            $step3Start = microtime(true);
            $trackDetailsMap = $this->getTrackDetailsBatch($allTrackUids, $currentUserUid);
            $step3Time = microtime(true) - $step3Start;
            error_log("EventService::getEventInformationOptimized - Step 3 (batch track details) took: " . number_format($step3Time * 1000, 2) . "ms");
        }

        // Step 4: Build event information representations
        $step4Start = microtime(true);
        foreach ($eventTrackMap as $eventUid => $eventData) {
            $tracksarray = [];
            
            foreach ($eventData['tracks'] as $track) {
                $trackUid = $track->getTrackUid();
                
                // Get pre-fetched data
                $trackstats = $trackStatsMap[$trackUid] ?? null;
                $trackDetails = $trackDetailsMap[$trackUid] ?? null;
                
                // Use pre-fetched track details or fallback to individual fetch
                $trackRepresentation = $trackDetails ?: 
                    $this->trackInformationAssembly->toRepresentation(
                        $this->trackservice->getTrackByTrackUid($trackUid, $currentUserUid), 
                        $permissions, 
                        $currentUserUid, 
                        $trackstats
                    );
                
                $tracksarray[] = $trackRepresentation;
            }
            
            $eventInfos[] = $this->eventInformationAssembly->toRepresentationWithTracks(
                $eventData['event'], 
                $tracksarray, 
                $permissions, 
                $currentUserUid,
                $eventData['tracks'] // Pass the pre-fetched Track objects
            );
        }
        $step4Time = microtime(true) - $step4Start;
        error_log("EventService::getEventInformationOptimized - Step 4 (build representations) took: " . number_format($step4Time * 1000, 2) . "ms");

        $totalTime = microtime(true) - $startTime;
        error_log("EventService::getEventInformationOptimized END - Total time: " . number_format($totalTime * 1000, 2) . "ms");

        return $eventInfos;
    }

    /**
     * Batch fetch tracks for multiple events
     * 
     * @param array $eventUids Array of event UIDs
     * @param string $currentUserUid Current user UID
     * @return array Array of Track objects
     */
    private function getTracksForEventsBatch(array $eventUids, string $currentUserUid): array
    {
        if (empty($eventUids)) {
            return [];
        }

        $startTime = microtime(true);
        error_log("EventService::getTracksForEventsBatch START - processing " . count($eventUids) . " event UIDs");

        try {
            // Use a single query to get all tracks for all events
            $placeholders = str_repeat('?,', count($eventUids) - 1) . '?';
            $sql = "SELECT * FROM track WHERE event_uid IN ($placeholders)";
            
            $queryStart = microtime(true);
            $statement = $this->trackservice->getTrackRepository()->connection->prepare($sql);
            $statement->execute($eventUids);
            $tracks = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Track\Track::class, null);
            $queryTime = microtime(true) - $queryStart;
            error_log("EventService::getTracksForEventsBatch - SQL query took: " . number_format($queryTime * 1000, 2) . "ms, returned " . count($tracks) . " tracks");
            
        } catch (PDOException $e) {
            error_log("Error in batch tracks for events: " . $e->getMessage());
            return [];
        }
        
        $totalTime = microtime(true) - $startTime;
        error_log("EventService::getTracksForEventsBatch END - Total time: " . number_format($totalTime * 1000, 2) . "ms");
        
        return $tracks;
    }

    /**
     * Batch fetch track statistics for multiple tracks
     * 
     * @param array $trackUids Array of track UIDs
     * @return array Map of track_uid => TrackStatistics
     */
    private function getTrackStatisticsBatch(array $trackUids): array
    {
        $startTime = microtime(true);
        error_log("EventService::getTrackStatisticsBatch START - processing " . count($trackUids) . " track UIDs");
        
        if (empty($trackUids)) {
            return [];
        }

        $trackStatsMap = [];
        
        // Create placeholders for IN clause
        $placeholders = str_repeat('?,', count($trackUids) - 1) . '?';
        
        // Single query to get all track statistics
        $sql = "SELECT track_uid, countparticipants as countParticipants, dns as countDns, dnf as countDnf, completed as countFinished 
                FROM v_race_statistic 
                WHERE track_uid IN ($placeholders)";
        
        try {
            $queryStart = microtime(true);
            $statement = $this->statisticsRepository->connection->prepare($sql);
            $statement->execute($trackUids);
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            $queryTime = microtime(true) - $queryStart;
            error_log("EventService::getTrackStatisticsBatch - SQL query took: " . number_format($queryTime * 1000, 2) . "ms, returned " . count($results) . " rows");
            
            $processStart = microtime(true);
            foreach ($results as $row) {
                $trackStats = new \App\Domain\Model\Stats\TrackStatistics();
                $trackStats->setCountParticipants((int)$row['countParticipants']);
                $trackStats->setCountDns((int)$row['countDns']);
                $trackStats->setCountDnf((int)$row['countDnf']);
                $trackStats->setCountFinished((int)$row['countFinished']);
                
                $trackStatsMap[$row['track_uid']] = $trackStats;
            }
            $processTime = microtime(true) - $processStart;
            error_log("EventService::getTrackStatisticsBatch - Processing results took: " . number_format($processTime * 1000, 2) . "ms");
            
        } catch (PDOException $e) {
            error_log("Error in batch track statistics: " . $e->getMessage());
        }
        
        $totalTime = microtime(true) - $startTime;
        error_log("EventService::getTrackStatisticsBatch END - Total time: " . number_format($totalTime * 1000, 2) . "ms, returned " . count($trackStatsMap) . " track stats");
        
        return $trackStatsMap;
    }

    /**
     * Batch fetch track details for multiple tracks
     * 
     * @param array $trackUids Array of track UIDs
     * @param string $currentUserUid Current user UID
     * @return array Map of track_uid => TrackRepresentation
     */
    private function getTrackDetailsBatch(array $trackUids, string $currentUserUid): array
    {
        $startTime = microtime(true);
        error_log("EventService::getTrackDetailsBatch START - processing " . count($trackUids) . " track UIDs");
        
        if (empty($trackUids)) {
            return [];
        }

        $trackDetailsMap = [];
        $permissions = $this->getPermissions($currentUserUid);
        
        // Get all tracks in one query
        $placeholders = str_repeat('?,', count($trackUids) - 1) . '?';
        $sql = "SELECT * FROM track WHERE track_uid IN ($placeholders)";
        
        try {
            $tracksQueryStart = microtime(true);
            $statement = $this->trackservice->getTrackRepository()->connection->prepare($sql);
            $statement->execute($trackUids);
            $tracks = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Track\Track::class, null);
            $tracksQueryTime = microtime(true) - $tracksQueryStart;
            error_log("EventService::getTrackDetailsBatch - Tracks query took: " . number_format($tracksQueryTime * 1000, 2) . "ms, returned " . count($tracks) . " tracks");
            
            // Get all checkpoints for all tracks in one query
            $checkpointQueryStart = microtime(true);
            $checkpointSql = "SELECT tc.track_uid, tc.checkpoint_uid 
                             FROM track_checkpoint tc 
                             WHERE tc.track_uid IN ($placeholders)";
            $checkpointStatement = $this->trackservice->getTrackRepository()->connection->prepare($checkpointSql);
            $checkpointStatement->execute($trackUids);
            $checkpointResults = $checkpointStatement->fetchAll(PDO::FETCH_ASSOC);
            $checkpointQueryTime = microtime(true) - $checkpointQueryStart;
            error_log("EventService::getTrackDetailsBatch - Checkpoints query took: " . number_format($checkpointQueryTime * 1000, 2) . "ms, returned " . count($checkpointResults) . " checkpoints");
            
            // Group checkpoints by track
            $groupStart = microtime(true);
            $trackCheckpoints = [];
            foreach ($checkpointResults as $row) {
                if (!isset($trackCheckpoints[$row['track_uid']])) {
                    $trackCheckpoints[$row['track_uid']] = [];
                }
                $trackCheckpoints[$row['track_uid']][] = $row['checkpoint_uid'];
            }
            $groupTime = microtime(true) - $groupStart;
            error_log("EventService::getTrackDetailsBatch - Grouping checkpoints took: " . number_format($groupTime * 1000, 2) . "ms");
            
            // Build track representations
            $buildStart = microtime(true);
            foreach ($tracks as $track) {
                $trackUid = $track->getTrackUid();
                
                // Set checkpoints for this track
                if (isset($trackCheckpoints[$trackUid])) {
                    $track->setCheckpoints($trackCheckpoints[$trackUid]);
                }
                
                // Create track representation using TrackInformationAssembly to match the fallback format
                $trackRepresentation = $this->trackservice->getTrackAssembly()->toRepresentation($track, $permissions, $currentUserUid);
                $trackInformationRepresentation = $this->trackInformationAssembly->toRepresentation($trackRepresentation, $permissions, $currentUserUid, null);
                $trackDetailsMap[$trackUid] = $trackInformationRepresentation;
            }
            $buildTime = microtime(true) - $buildStart;
            error_log("EventService::getTrackDetailsBatch - Building representations took: " . number_format($buildTime * 1000, 2) . "ms");
            
        } catch (PDOException $e) {
            error_log("Error in batch track details: " . $e->getMessage());
        }
        
        $totalTime = microtime(true) - $startTime;
        error_log("EventService::getTrackDetailsBatch END - Total time: " . number_format($totalTime * 1000, 2) . "ms, returned " . count($trackDetailsMap) . " track details");
        
        return $trackDetailsMap;
    }
    
    /**
     * Create an EventGroupDTO from an Event
     * 
     * @param Event $event The event to convert
     * @return EventGroupDTO The event group DTO
     */
    private function createEventGroupFromEvent(Event $event): EventGroupDTO
    {
        $eventGroupDTO = new EventGroupDTO();
        $eventGroupDTO->uid = $event->getEventUid();
        $eventGroupDTO->name = $event->getTitle();
        $eventGroupDTO->description = $event->getDescription();
        $eventGroupDTO->startdate = $this->formatDate($event->getStartdate());
        $eventGroupDTO->enddate = $this->formatDate($event->getEnddate());
        $eventGroupDTO->active = $event->isActive();
        $eventGroupDTO->canceled = $event->isCanceled();
        $eventGroupDTO->completed = $event->isCompleted();
        $eventGroupDTO->event_uids = [];
        return $eventGroupDTO;
    }
    
    /**
     * Update an existing EventGroupDTO with data from an Event
     * 
     * @param EventGroupDTO $eventGroupDTO The event group DTO to update
     * @param Event $event The event with the new data
     * @return EventGroupDTO The updated event group DTO
     */
    private function updateEventGroupFromEvent(EventGroupDTO $eventGroupDTO, Event $event): EventGroupDTO
    {
        $eventGroupDTO->name = $event->getTitle();
        $eventGroupDTO->description = $event->getDescription();
        $eventGroupDTO->startdate = $this->formatDate($event->getStartdate());
        $eventGroupDTO->enddate = $this->formatDate($event->getEnddate());
        $eventGroupDTO->active = $event->isActive();
        $eventGroupDTO->canceled = $event->isCanceled();
        $eventGroupDTO->completed = $event->isCompleted();
        if (!isset($eventGroupDTO->event_uids) || !is_array($eventGroupDTO->event_uids)) {
            $eventGroupDTO->event_uids = [];
        }
        return $eventGroupDTO;
    }
    
    /**
     * Format a date to YYYY-MM-DD format
     * 
     * @param mixed $date The date to format
     * @return string The formatted date
     */
    private function formatDate($date): string
    {
        if (is_string($date)) {
            return $date;
        }
        
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d');
        }
        
        // Default fallback
        return date('Y-m-d', strtotime($date));
    }
    
    /**
     * Format a date for MySQL database storage
     * Handles JavaScript ISO date strings and converts them to MySQL-compatible format
     * 
     * @param mixed $date The date to format
     * @return string|null The formatted date
     */
    private function formatDateForDatabase($date): ?string
    {
        if (is_null($date) || $date === '') {
            return null;
        }
        
        if (is_string($date)) {
            // Handle JavaScript ISO date format (e.g., "2026-12-29T23:00:00.000Z")
            if (preg_match('/^\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $date)) {
                try {
                    $dateTime = new \DateTime($date);
                    return $dateTime->format('Y-m-d');
                } catch (\Exception $e) {
                    error_log("Failed to parse ISO date: " . $date . " - " . $e->getMessage());
                    return date('Y-m-d');
                }
            }
            
            // If it's already in a proper format, return as is
            if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)) {
                return $date;
            }
            
            // Try to parse other string formats
            try {
                return date('Y-m-d', strtotime($date));
            } catch (\Exception $e) {
                error_log("Failed to parse date string: " . $date . " - " . $e->getMessage());
                return date('Y-m-d');
            }
        }
        
        if ($date instanceof \DateTime) {
            return $date->format('Y-m-d');
        }
        
        // Default fallback
        try {
            return date('Y-m-d', strtotime($date));
        } catch (\Exception $e) {
            error_log("Failed to format date: " . print_r($date, true) . " - " . $e->getMessage());
            return date('Y-m-d');
        }
    }

}
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
                        // Failed to update event group in LoppService, but continuing with local update
                    } else {
                        // Successfully updated event group in LoppService
                    }
                } else {
                    // Event group doesn't exist in LoppService, try to create it
                    $eventGroupDTO = $this->createEventGroupFromEvent($event);
                    
                    $createdEventGroup = $this->eventGroupRestClient->createEventGroup($eventGroupDTO);
                    
                    if (!$createdEventGroup) {
                        // Failed to create event group in LoppService, but continuing with local update
                    } else {
                        // Successfully created event group in LoppService
                    }
                }
            } catch (\Exception $loppServiceException) {
                // Only skip LoppService for connection errors, otherwise re-throw
                $errorMessage = $loppServiceException->getMessage();
                if (strpos($errorMessage, 'Could not resolve host') !== false || 
                    strpos($errorMessage, 'Connection refused') !== false ||
                    strpos($errorMessage, 'Connection timed out') !== false) {
                    // Connection issue - continue with local event update only
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
            
            // Failed to update event with rollback
            
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
                
                // Create event group in LoppService
                
                $createdEventGroup = $this->eventGroupRestClient->createEventGroup($eventGroupDTO);
        
                if (!$createdEventGroup) {
                    // Failed to create event group in LoppService, but continuing with local event
                } else {
                    // Successfully created event group in LoppService
                }
            } catch (\Exception $loppServiceException) {
                // Only skip LoppService for connection errors, otherwise re-throw
                $errorMessage = $loppServiceException->getMessage();
                if (strpos($errorMessage, 'Could not resolve host') !== false || 
                    strpos($errorMessage, 'Connection refused') !== false ||
                    strpos($errorMessage, 'Connection timed out') !== false) {
                    // Connection issue - continue with local event creation only
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
            // Failed to create event with rollback
            
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
                    // Event group not found in LoppService (this is OK)
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
        $permissions = $this->getPermissions($currentUserUid);
        $eventInfos = array();

        if ($eventUid != "") {
            // Single event optimization
            $event = $this->eventRepository->eventFor($eventUid);
            
            if (!$event) {
                return [];
            }
            
            $eventInfos = $this->getEventInformationOptimized([$event], $currentUserUid, $permissions);
        } else {
            // Multiple events optimization
            $events = $this->eventRepository->allEvents();
            $eventInfos = $this->getEventInformationOptimized($events, $currentUserUid, $permissions);
        }

        // Sort eventInfos by startdate (desc) and then by title
        usort($eventInfos, function($a, $b) {
            $dateCompare = strtotime($b->getEvent()->getStartdate()) - strtotime($a->getEvent()->getStartdate());
            if ($dateCompare === 0) {
                return strcmp($b->getEvent()->getTitle(), $a->getEvent()->getTitle());
            }
            return $dateCompare;
        });

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

        // Step 2: Batch fetch all track statistics in one query
        $trackStatsMap = [];
        if (!empty($allTrackUids)) {
            $trackStatsMap = $this->getTrackStatisticsBatch($allTrackUids);
        }

        // Step 3: Batch fetch all track details in one query
        $trackDetailsMap = [];
        if (!empty($allTrackUids)) {
            $trackDetailsMap = $this->getTrackDetailsBatch($allTrackUids, $currentUserUid);
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
        // Build representations completed

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

        try {
            // Use a single query to get all tracks for all events
            $placeholders = str_repeat('?,', count($eventUids) - 1) . '?';
            $sql = "SELECT * FROM track WHERE event_uid IN ($placeholders)";
            
            $statement = $this->trackservice->getTrackRepository()->connection->prepare($sql);
            $statement->execute($eventUids);
            $tracks = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Track\Track::class, null);
            
        } catch (PDOException $e) {
            return [];
        }
        
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
            $statement = $this->statisticsRepository->connection->prepare($sql);
            $statement->execute($trackUids);
            $results = $statement->fetchAll(PDO::FETCH_ASSOC);
            
            foreach ($results as $row) {
                $trackStats = new \App\Domain\Model\Stats\TrackStatistics();
                $trackStats->setCountParticipants((int)$row['countParticipants']);
                $trackStats->setCountDns((int)$row['countDns']);
                $trackStats->setCountDnf((int)$row['countDnf']);
                $trackStats->setCountFinished((int)$row['countFinished']);
                
                $trackStatsMap[$row['track_uid']] = $trackStats;
            }
            
        } catch (PDOException $e) {
            // Error in batch track statistics
        }
        
        // Track statistics batch processing completed
        
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
        if (empty($trackUids)) {
            return [];
        }

        $trackDetailsMap = [];
        $permissions = $this->getPermissions($currentUserUid);
        
        // Get all tracks in one query
        $placeholders = str_repeat('?,', count($trackUids) - 1) . '?';
        $sql = "SELECT * FROM track WHERE track_uid IN ($placeholders)";
        
        try {
            $statement = $this->trackservice->getTrackRepository()->connection->prepare($sql);
            $statement->execute($trackUids);
            $tracks = $statement->fetchAll(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, \App\Domain\Model\Track\Track::class, null);
            
            // Get all checkpoints for all tracks in one query
            $checkpointSql = "SELECT tc.track_uid, tc.checkpoint_uid 
                             FROM track_checkpoint tc 
                             WHERE tc.track_uid IN ($placeholders)";
            $checkpointStatement = $this->trackservice->getTrackRepository()->connection->prepare($checkpointSql);
            $checkpointStatement->execute($trackUids);
            $checkpointResults = $checkpointStatement->fetchAll(PDO::FETCH_ASSOC);
            
            // Group checkpoints by track
            $trackCheckpoints = [];
            foreach ($checkpointResults as $row) {
                if (!isset($trackCheckpoints[$row['track_uid']])) {
                    $trackCheckpoints[$row['track_uid']] = [];
                }
                $trackCheckpoints[$row['track_uid']][] = $row['checkpoint_uid'];
            }
            
            // Build track representations
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
            
        } catch (PDOException $e) {
            // Error in batch track details
        }
        
        // Track details batch processing completed
        
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
            return date('Y-m-d');
        }
    }

}
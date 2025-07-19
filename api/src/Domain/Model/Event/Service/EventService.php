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

    public function allEvents(): array
    {
        $events = $this->eventRepository->allEvents();
        if (!isset($events)) {
            return array();
        }
        $userContext = \App\common\Context\UserContext::getInstance();
        $currentUserUid = $userContext->getUserId();
        return $this->eventAssembly->toRepresentations($events, $currentUserUid);
    }

    public function eventFor(string $event_uid)
    {
        $event = $this->eventRepository->eventFor($event_uid);
        if (!isset($event)) {
            return null;
        }
        $userContext = \App\common\Context\UserContext::getInstance();
        $currentUserUid = $userContext->getUserId();
        $permissions = $this->getPermissions($currentUserUid);
        return $this->eventAssembly->toRepresentation($event, $permissions);
    }

    public function updateEvent(string $event_uid, EventRepresentation $eventRepresentation): EventRepresentation
    {
        $event = $this->toEvent($eventRepresentation);
        $organizerId = $this->eventRepository->getOrganizerIdFromContext();
        if ($organizerId !== null && $event->getOrganizerId() === null) {
            $event->setOrganizerId($organizerId);
        }
        $userContext = \App\common\Context\UserContext::getInstance();
        $currentUserUid = $userContext->getUserId();
        $permissions = $this->getPermissions($currentUserUid);
        $eventexists = $this->eventRepository->eventFor($event_uid);
        if(!$eventexists){
            throw new BrevetException("Event not found", 1, null);
        }
        $connection = $this->eventRepository->getConnection();
        $connection->beginTransaction();
        try {
            $event = $this->eventRepository->updateEvent($event_uid, $event);
            try {
                $eventGroup = $this->eventGroupRestClient->getEventGroupById($event->getEventUid());
                if ($eventGroup) {
                    $eventGroupDTO = $this->updateEventGroupFromEvent($eventGroup, $event);
                    $updatedEventGroup = $this->eventGroupRestClient->updateEventGroup($event->getEventUid(), $eventGroupDTO);
                    if (!$updatedEventGroup) {
                        error_log("Warning: Failed to update event group in LoppService, but continuing with local update: " . $event->getEventUid());
                    } else {
                        error_log("Successfully updated event group in LoppService: " . $event->getEventUid());
                    }
                } else {
                    $eventGroupDTO = $this->createEventGroupFromEvent($event);
                    $createdEventGroup = $this->eventGroupRestClient->createEventGroup($eventGroupDTO);
                    if (!$createdEventGroup) {
                        error_log("Warning: Failed to create event group in LoppService, but continuing with local update: " . $event->getEventUid());
                    } else {
                        error_log("Successfully created event group in LoppService: " . $event->getEventUid());
                    }
                }
            } catch (\Exception $loppServiceException) {
                $errorMessage = $loppServiceException->getMessage();
                if (strpos($errorMessage, 'Could not resolve host') !== false || 
                    strpos($errorMessage, 'Connection refused') !== false ||
                    strpos($errorMessage, 'Connection timed out') !== false) {
                    error_log("LoppService connection failed for event " . $event->getEventUid() . ": " . $errorMessage);
                    error_log("Continuing with local event update only");
                } else {
                    throw $loppServiceException;
                }
            }
            $connection->commit();
            return $this->eventAssembly->toRepresentation($event, $permissions);
        } catch (\Exception $e) {
            $connection->rollBack();
            error_log("Failed to update event with rollback: " . $e->getMessage());
            error_log("Event data: " . json_encode([
                'uid' => $event->getEventUid(),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'startdate' => $event->getStartdate(),
                'enddate' => $event->getEnddate()
            ]));
            throw new BrevetException("Det gick inte att uppdatera event: " . $e->getMessage(), 14, $e);
        }
    }

    public function createEvent(EventRepresentation $eventRepresentation)
    {
        $event = $this->toEvent($eventRepresentation);
        $event->setEventUid((string) Uuid::uuid4());
        $organizerId = $this->eventRepository->getOrganizerIdFromContext();
        if ($organizerId !== null && $event->getOrganizerId() === null) {
            $event->setOrganizerId($organizerId);
        }
        $userContext = \App\common\Context\UserContext::getInstance();
        $currentUserUid = $userContext->getUserId();
        $permissions = $this->getPermissions($currentUserUid);
        $connection = $this->eventRepository->getConnection();
        $connection->beginTransaction();
        try {
            $event = $this->eventRepository->createEvent($event);
            try {
                $eventGroupDTO = $this->createEventGroupFromEvent($event);
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
                $errorMessage = $loppServiceException->getMessage();
                if (strpos($errorMessage, 'Could not resolve host') !== false || 
                    strpos($errorMessage, 'Connection refused') !== false ||
                    strpos($errorMessage, 'Connection timed out') !== false) {
                    error_log("LoppService connection failed for new event " . $event->getEventUid() . ": " . $errorMessage);
                    error_log("Continuing with local event creation only");
                } else {
                    throw $loppServiceException;
                }
            }
            $connection->commit();
            return $this->eventAssembly->toRepresentation($event, $permissions);
        } catch (\Exception $e) {
            $connection->rollBack();
            print_r($e->getMessage());
            error_log("Failed to create event with rollback. Error: " . $e->getMessage());
            error_log("Event data: " . json_encode([
                'uid' => $event->getEventUid(),
                'title' => $event->getTitle(),
                'description' => $event->getDescription(),
                'startdate' => $event->getStartdate(),
                'enddate' => $event->getEnddate()
            ]));
            throw new BrevetException("Det gick inte att skapa event: " . $e->getMessage(), 11, $e);
        }
    }

    public function deleteEvent(string $event_uid)
    {
        $userContext = \App\common\Context\UserContext::getInstance();
        $currentUserUid = $userContext->getUserId();
        $tracks = $this->trackservice->tracksForEvent($currentUserUid, $event_uid);
        if (!empty($tracks)) {
            throw new BrevetException("Det finns banor kopplade till eventet. Banorna måste tas bort från eventet", 5, null);
        }
        
        $connection = $this->eventRepository->getConnection();
        try {
            $connection->beginTransaction();
            
            // Delete from local database first
            $this->eventRepository->deleteEvent($event_uid);
            
            // Try to delete corresponding event group from loppservice
            try {
                $deleteSuccess = $this->eventGroupRestClient->deleteEventGroup($event_uid);
                
                if ($deleteSuccess) {
                    error_log("Successfully deleted event group from loppservice: " . $event_uid);
                } else {
                    // Check if the event group exists first to determine if the failure is due to "not found"
                    $existingEventGroup = $this->eventGroupRestClient->getEventGroupById($event_uid);
                    if ($existingEventGroup === null) {
                        error_log("Event group not found in loppservice (this is OK): " . $event_uid);
                    } else {
                        error_log("Failed to delete event group from loppservice (event group exists but deletion failed): " . $event_uid);
                    }
                }
            } catch (\Exception $loppServiceException) {
                $errorMessage = $loppServiceException->getMessage();
                if (strpos($errorMessage, 'Could not resolve host') !== false || 
                    strpos($errorMessage, 'Connection refused') !== false ||
                    strpos($errorMessage, 'Connection timed out') !== false) {
                    error_log("LoppService connection failed for event deletion " . $event_uid . ": " . $errorMessage);
                    error_log("Continuing with local event deletion only");
                } else {
                    // Log the error but don't fail the main deletion
                    error_log("Error deleting event group in loppservice: " . $loppServiceException->getMessage());
                    error_log("Event deletion completed locally, but loppservice sync failed for event: " . $event_uid);
                }
            }
            
            $connection->commit();
        } catch (\Exception $e) {
            $connection->rollBack();
            throw new BrevetException("Det gick inte att ta bort event: " . $e->getMessage(), 12, $e);
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
        $event->setOrganizerId($eventRepresentation->getOrganizerId());
        
        // Format dates properly for MySQL storage
        $event->setStartdate($this->formatDateForDatabase($eventRepresentation->getStartdate()));
        $event->setEnddate($this->formatDateForDatabase($eventRepresentation->getEnddate()));

        return $event;
    }

    public function getPermissions($user_uid): array
    {
        // If user_uid is null, try to get it from UserContext
        if ($user_uid === null) {
            $userContext = \App\common\Context\UserContext::getInstance();
            $user_uid = $userContext->getUserId();
        }
        
        // If still null, return empty permissions array
        if ($user_uid === null) {
            return [];
        }
        
        return $this->permissinrepository->getPermissionsTodata("EVENT", $user_uid);
    }

    public function eventInformation(string $eventUid): array
    {
        $userContext = \App\common\Context\UserContext::getInstance();
        $currentUserUid = $userContext->getUserId();
        $permissions = $this->getPermissions($currentUserUid);
        $eventInfos = array();
        $tracksarray = array();

        if ($eventUid != "") {
            $event = $this->eventRepository->eventFor($eventUid);
            $tracks = $this->trackservice->tracksForEvent($currentUserUid, $event->getEventUid());
            foreach ($tracks as $track) {
                // $participants = $this->participantService->participantsOnTrack($track->getTrackUid(), $currentUserUid);
                $trackstats = $this->statisticsRepository->statsForTrack($track->getTrackUid());
                array_push($tracksarray, $this->trackInformationAssembly->toRepresentation($this->trackservice->getTrackByTrackUid($track->getTrackUid(), $currentUserUid), $permissions, $currentUserUid, $trackstats == null ? null : $trackstats));
            }
            array_push($eventInfos, $this->eventInformationAssembly->toRepresentation($event, $tracksarray, $permissions, $currentUserUid));
        } else {

            $events = $this->eventRepository->allEvents();
            foreach ($events as $event) {
                $tracksarray = [];
                $tracks = $this->trackservice->tracksForEvent($currentUserUid, $event->getEventUid());
                foreach ($tracks as $track) {
                    //$participants = $this->participantService->participantsOnTrack($track->getTrackUid(), $currentUserUid);
                    $trackstats = $this->statisticsRepository->statsForTrack($track->getTrackUid());
                    array_push($tracksarray, $this->trackInformationAssembly->toRepresentation($this->trackservice->getTrackByTrackUid($track->getTrackUid(), $currentUserUid), $permissions, $currentUserUid, $trackstats == null ? null : $trackstats));
                }
                array_push($eventInfos, $this->eventInformationAssembly->toRepresentation($event, $tracksarray, $permissions, $currentUserUid));
            }

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
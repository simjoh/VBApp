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
            // Update event in local database
            $event = $this->eventRepository->updateEvent($event_uid, $event);
            
            // Try to get the event group by event UID
            $eventGroup = $this->eventGroupRestClient->getEventGroupById($event->getEventUid());
    
            if ($eventGroup) {
                // Update the existing event group
                $eventGroupDTO = $this->updateEventGroupFromEvent($eventGroup, $event);
                
           
                
                $updatedEventGroup = $this->eventGroupRestClient->updateEventGroup($event->getEventUid(), $eventGroupDTO);
                
                if (!$updatedEventGroup) {
                    $connection->rollBack();
                    throw new BrevetException("Det gick inte att uppdatera eventgrupp i loppservice: Inget svar mottogs", 12, null);
                }
            } else {
                // Event group doesn't exist in LoppService, create it
                $eventGroupDTO = $this->createEventGroupFromEvent($event);
                
          
                $createdEventGroup = $this->eventGroupRestClient->createEventGroup($eventGroupDTO);
                
                if (!$createdEventGroup) {
                    $connection->rollBack();
                    throw new BrevetException("Det gick inte att skapa eventgrupp i loppservice: Inget svar mottogs", 13, null);
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

            // Create corresponding event group in loppservice with our UID
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
    
            if ($createdEventGroup) {
                $connection->commit();
                return $this->eventAssembly->toRepresentation($event, $permissions);
            } else {
                $connection->rollBack();
                throw new BrevetException("Det gick inte att skapa eventgrupp i loppservice: Inget svar mottogs", 10, null);
            }
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
        $event->setStartdate($eventRepresentation->getStartdate());
        $event->setEnddate($eventRepresentation->getEnddate());

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
        
        // Get tracks associated with this event using the current user's UID
        // Since we're in a system context, we'll use a special system user UID
        $systemUserUid = 'system'; // You might want to use a real system user UID here
        $tracks = $this->trackservice->tracksForEvent($systemUserUid, $event->getEventUid());
        
        // Initialize event_uids array with track UIDs
        $eventGroupDTO->event_uids = [];
        
        // Add track UIDs to event_uids
        if (!empty($tracks)) {
            foreach ($tracks as $track) {
                $eventGroupDTO->event_uids[] = $track->getTrackUid();
            }
        }

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
        
        // Get tracks associated with this event using the current user's UID
        // Since we're in a system context, we'll use a special system user UID
        $systemUserUid = 'system'; // You might want to use a real system user UID here
        $tracks = $this->trackservice->tracksForEvent($systemUserUid, $event->getEventUid());
        
        // Initialize event_uids array if not set
        if (!isset($eventGroupDTO->event_uids) || !is_array($eventGroupDTO->event_uids)) {
            $eventGroupDTO->event_uids = [];
        }
        
        // Add track UIDs to event_uids
        if (!empty($tracks)) {
            foreach ($tracks as $track) {
                $trackUid = $track->getTrackUid();
                if (!in_array($trackUid, $eventGroupDTO->event_uids)) {
                    $eventGroupDTO->event_uids[] = $trackUid;
                }
            }
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

}
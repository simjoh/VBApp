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

class EventService extends ServiceAbstract
{

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
        $permissions = $this->getPermissions($currentUserUid);
        $event = $this->eventRepository->updateEvent($event_uid, $this->toEvent($eventRepresentation));
        return $this->eventAssembly->toRepresentation($event, $permissions);
    }

    public function createEvent(EventRepresentation $eventRepresentation, string $currentUserUid)
    {
        $permissions = $this->getPermissions($currentUserUid);
        $event = $this->eventRepository->createEvent($this->toEvent($eventRepresentation));

        return $this->eventAssembly->toRepresentation($event, $permissions);
    }

    public function deleteEvent(string $event_uid, string $currentUserUid)
    {

        $tracks = $this->trackservice->tracksForEvent($currentUserUid, $event_uid);

        if ($tracks == null || count($tracks) == 0) {
            $this->eventRepository->deleteEvent($event_uid);
        } else {
            throw new BrevetException("Det finns banor kopplade till eventet. Banorna måste tas bort från eventet", 5, null);
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
//        print_r($eventInfos);
        return $eventInfos;
    }




}
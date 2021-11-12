<?php

namespace App\Domain\Model\Event\Service;


use App\common\Rest\Link;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\Event\Event;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class EventService extends ServiceAbstract
{

    public function __construct(ContainerInterface $c, EventRepository $eventRepository, PermissionRepository $permissionRepository)
    {
        $this->eventRepository = $eventRepository;
        $this->permissinrepository = $permissionRepository;
    }

    public function allEvents(string $currentUserUid): array {
        $events = $this->eventRepository->allEvents();
        if(!isset($events)){
            return array();
        }
        return $this->toRepresentations($events,$currentUserUid);
    }

    public function eventFor(string $event_uid, string $currentUserUid)
    {
        $event = $this->eventRepository->eventFor($event_uid);
        if(!isset($event)){
            return null;
        }
        $permissions = $this->getPermissions($currentUserUid);
        return $this->toRepresentation($event,$permissions );
    }

    public function updateEvent(string $event_uid, EventRepresentation $eventRepresentation,string $currentUserUid): EventRepresentation
    {
        $permissions = $this->getPermissions($currentUserUid);
        $event = $this->eventRepository->updateEvent($event_uid ,$this->toEvent($eventRepresentation));
        return $this->toRepresentation($event,$permissions);
    }

    public function createEvent(EventRepresentation $eventRepresentation,string $currentUserUid)
    {
        $permissions = $this->getPermissions($currentUserUid);
        $event = $this->eventRepository->createEvent($this->toEvent($eventRepresentation));

        return $this->toRepresentation($event,$permissions);
    }

    public function deleteEvent(string $event_uid)
    {
        $this->eventRepository->deleteEvent($event_uid);
    }

    private function toEvent(EventRepresentation $eventRepresentation): Event {

        $event = new Event();
        $event->setEventUid($eventRepresentation->getEventUid());
        $event->setDescription($eventRepresentation->getDescription());
        $event->setTitle($eventRepresentation->getTitle());
        $event->setActive($eventRepresentation->isActive());
        $event->setCanceled($eventRepresentation->isCanceled());
        $event->setCompleted($eventRepresentation->isCompleted());
        $event->setStartdate($eventRepresentation->getStartdate());
        $event->setEnddate($eventRepresentation->getEnddate());

        return $event;
    }

    private function toRepresentations(array $eventsArray, string $currentUserUid): array {


        $permissions = $this->getPermissions($currentUserUid);

        $events = array();
        foreach ($eventsArray as $x =>  $event) {
            array_push($events, (object) $this->toRepresentation($event,$permissions));
        }
        return $events;
    }

    private function toRepresentation(Event $event,  array $permissions): EventRepresentation {

        $eventrepresentation = new EventRepresentation();
        $eventrepresentation->setDescription($event->getDescription() == null ? 0 : $event->getDescription());
        $eventrepresentation->setTitle($event->getTitle() == null ? null : $event->getTitle());
        $eventrepresentation->setEventUid($event->getEventUid());
        $eventrepresentation->setActive($event->isActive());
        $eventrepresentation->setCanceled($event->isCanceled());
        $eventrepresentation->setCompleted($event->isCompleted());
        $eventrepresentation->setStartdate($event->getStartdate());
        $eventrepresentation->setEnddate($event->getEnddate());

        $linkArray = array();
        foreach ($permissions as $x =>  $site) {
            if($site->hasWritePermission()){
                array_push($linkArray, new Link("relation.event.update", 'PUT', '/api/user/' . $event->getEventUid()));
                array_push($linkArray, new Link("relation.event.delete", 'DELETE', '/api/user/' . $event->getEventUid()));
                break;
            }
            if($site->hasReadPermission()){
                array_push($linkArray, new Link("self", 'GET', '/api/user/' . $event->getEventUid()));
            };
        }

        $eventrepresentation->setLinks($linkArray);


        return $eventrepresentation;
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("EVENT",$user_uid);

    }
}
<?php

namespace App\Domain\Model\Event\Service;

use App\common\Service\ServiceAbstract;
use App\Domain\Model\Event\Event;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Event\Rest\EventAssembly;
use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class EventService extends ServiceAbstract
{

    public function __construct(ContainerInterface $c, EventRepository $eventRepository, PermissionRepository $permissionRepository, EventAssembly $eventAssembly)
    {
        $this->eventRepository = $eventRepository;
        $this->permissinrepository = $permissionRepository;
        $this->eventAssembly = $eventAssembly;
    }

    public function allEvents(string $currentUserUid): array {
        $events = $this->eventRepository->allEvents();
        if(!isset($events)){
            return array();
        }

        return $this->eventAssembly->toRepresentations($events,$currentUserUid);
    }

    public function eventFor(string $event_uid, string $currentUserUid)
    {
        $event = $this->eventRepository->eventFor($event_uid);
        if(!isset($event)){
            return null;
        }
        $permissions = $this->getPermissions($currentUserUid);
        return $this->eventAssembly->toRepresentation($event,$permissions );
    }

    public function updateEvent(string $event_uid, EventRepresentation $eventRepresentation,string $currentUserUid): EventRepresentation
    {
        $permissions = $this->getPermissions($currentUserUid);
        $event = $this->eventRepository->updateEvent($event_uid ,$this->toEvent($eventRepresentation));
        return $this->eventAssembly->toRepresentation($event,$permissions);
    }

    public function createEvent(EventRepresentation $eventRepresentation,string $currentUserUid)
    {
        $permissions = $this->getPermissions($currentUserUid);
        $event = $this->eventRepository->createEvent($this->toEvent($eventRepresentation));

        return $this->eventAssembly->toRepresentation($event,$permissions);
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

    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("EVENT",$user_uid);

    }
}
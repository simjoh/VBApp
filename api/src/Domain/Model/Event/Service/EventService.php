<?php

namespace App\Domain\Model\Event\Service;


use App\Domain\Model\Event\Event;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Event\Rest\EventRepresentation;
use Psr\Container\ContainerInterface;

class EventService
{

    public function __construct(ContainerInterface $c, EventRepository $eventRepository)
    {
        $this->eventRepository = $eventRepository;
    }

    public function allEvents(): array {
        $events = $this->eventRepository->allEvents();
        return $this->toRepresentations($events);
    }

    public function eventFor(string $event_uid)
    {
        $event = $this->eventRepository->eventFor($event_uid);
        return $this->toRepresentation($event);
    }

    public function updateEvent(string $event_uid, EventRepresentation $eventRepresentation): EventRepresentation
    {
        $event = $this->eventRepository->updateEvent($event_uid ,$this->toEvent($eventRepresentation));
        return $this->toRepresentation($event);
    }

    public function createEvent(EventRepresentation $eventRepresentation)
    {
        $event = $this->eventRepository->createEvent($this->toEvent($eventRepresentation));
        return $this->toRepresentation($event);

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

    private function toRepresentations(array $eventsArray): array {

        $events = array();
        foreach ($eventsArray as $x =>  $event) {
            array_push($events, (object) $this->toRepresentation($event));
        }
        return $events;
    }

    private function toRepresentation(Event $event): EventRepresentation {

        $eventrepresentation = new EventRepresentation();
        $eventrepresentation->setDescription($event->getDescription() == null ? 0 : $event->getDescription());
        $eventrepresentation->setTitle($event->getTitle() == null ? null : $event->getTitle());
        $eventrepresentation->setEventUid($event->getEventUid());
        $eventrepresentation->setActive($event->isActive());
        $eventrepresentation->setCanceled($event->isCanceled());
        $eventrepresentation->setCompleted($event->isCompleted());
        $eventrepresentation->setStartdate($event->getStartdate());
        $eventrepresentation->setEnddate($event->getEnddate());
        return $eventrepresentation;
    }




}
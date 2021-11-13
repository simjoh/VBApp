<?php

namespace App\Domain\Model\Event\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Event\Event;
use App\Domain\Permission\PermissionRepository;


class EventAssembly
{

    public function __construct(PermissionRepository $permissionRepository)
    {
        $this->permissinrepository = $permissionRepository;
    }

    public function toRepresentations(array $eventsArray, string $currentUserUid): array {

        $permissions = $this->getPermissions($currentUserUid);

        $events = array();
        foreach ($eventsArray as $x =>  $event) {
            array_push($events, (object) $this->toRepresentation($event,$permissions));
        }
        return $events;
    }

    public function toRepresentation(Event $event,  array $permissions): EventRepresentation {

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
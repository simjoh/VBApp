<?php

namespace App\Domain\Model\Event\Rest;

use App\Domain\Model\Event\Event;
use App\Domain\Model\Track\Rest\TrackAssembly;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class EventInformationAssembly
{
    private $permissinrepository;
    private $settings;
    private $eventAssembly;
    private $trackAssembly;

    public function __construct(PermissionRepository $permissionRepository, ContainerInterface $c, EventAssembly $eventAssembly, TrackAssembly $trackAssembly)
    {
        $this->permissinrepository = $permissionRepository;
        $this->settings = $c->get('settings');
        $this->eventAssembly = $eventAssembly;
        $this->trackAssembly = $trackAssembly;
    }

//    public function toRepresentations(array $eventsArray, string $currentUserUid): array {
//
//        $permissions = $this->getPermissions($currentUserUid);
//
//        $events = array();
//        foreach ($eventsArray as $x =>  $event) {
//            array_push($events, (object) $this->toRepresentation($event,$permissions));
//        }
//        return $events;
//    }

    public function toRepresentation(Event $event, array $tracks ,  array $permissions, string $currentUserUid): EventInformationRepresentation {
        $permissions = $this->getPermissions($currentUserUid);
        $eventinformationrepresentation = new EventInformationRepresentation();
        $eventinformationrepresentation->setEvent($this->eventAssembly->toRepresentation($event,$permissions));
        $eventinformationrepresentation->setTracks($tracks);

        return $eventinformationrepresentation;
    }

    /**
     * Create event information representation with pre-fetched tracks to avoid N+1 queries
     * 
     * @param Event $event The event
     * @param array $tracks Pre-fetched track representations
     * @param array $permissions User permissions
     * @param string $currentUserUid Current user UID
     * @param array $prefetchedTracks Pre-fetched Track objects for this event
     * @return EventInformationRepresentation
     */
    public function toRepresentationWithTracks(Event $event, array $tracks, array $permissions, string $currentUserUid, array $prefetchedTracks): EventInformationRepresentation {
        $permissions = $this->getPermissions($currentUserUid);
        $eventinformationrepresentation = new EventInformationRepresentation();
        $eventinformationrepresentation->setEvent($this->eventAssembly->toRepresentationWithTracks($event, $permissions, $prefetchedTracks));
        $eventinformationrepresentation->setTracks($tracks);

        return $eventinformationrepresentation;
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("EVENT",$user_uid);

    }

}
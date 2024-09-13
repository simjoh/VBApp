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

    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("EVENT",$user_uid);

    }

}
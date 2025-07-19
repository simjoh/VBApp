<?php

namespace App\Domain\Model\Event\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Event\Event;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;


class EventAssembly
{

    private $permissinrepository;
    private $settings;
    private $participantRepository;
    private $trackRepository;

    public function __construct(PermissionRepository $permissionRepository, ContainerInterface $c, ParticipantRepository $participantRepository, TrackRepository $trackRepository)
    {
        $this->permissinrepository = $permissionRepository;
        $this->settings = $c->get('settings');
        $this->participantRepository = $participantRepository;
        $this->trackRepository = $trackRepository;
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
        $eventrepresentation->setOrganizerId($event->getOrganizerId());
        $eventrepresentation->setCreatedAt($event->getCreatedAt());
        $eventrepresentation->setUpdatedAt($event->getUpdatedAt());

        $participantsarray = array();
        $linkArray = array();
        foreach ($permissions as $x =>  $site) {
          //  if($site->hasWritePermission()){
                array_push($linkArray, new Link("relation.event.update", 'PUT', $this->settings['path'] .'event/' . $event->getEventUid()));
                // ett event får inte tas bort om deltagare är tillagda
              $tracks =  $this->trackRepository->tracksbyEvent($event->getEventUid());
              foreach ($tracks as $track){
                  $participants = $this->participantRepository->participantsOnTrack($track->getTrackUid());
                  if(count($participants) > 0){
                      array_push($participantsarray, $participants);
                  }
              }
                if(count($participantsarray) == 0 && count($tracks) == 0){
                    array_push($linkArray, new Link("relation.event.delete", 'DELETE', $this->settings['path'] .'event/' . $event->getEventUid()));
                }

              //  break;
           // }

            if($site->hasReadPermission()){
                array_push($linkArray, new Link("self", 'GET', $this->settings['path'] . 'user/' . $event->getEventUid()));

                array_push($linkArray, new Link("relation.event.track", 'GET', $this->settings['path'] . 'tracker/event/' . $event->getEventUid()));
            };

            array_push($linkArray, new Link("relation.event.result", 'GET', $this->settings['path'] . 'results/event/' . $event->getEventUid()));

            // Add organizer link if organizer_id exists
            if($event->getOrganizerId() !== null){
                array_push($linkArray, new Link("relation.event.organizer", 'GET', $this->settings['path'] . 'organizer/' . $event->getOrganizerId()));
            }


        }

        $eventrepresentation->setLinks($linkArray);


        return $eventrepresentation;
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
        
        return $this->permissinrepository->getPermissionsTodata("EVENT",$user_uid);
    }



}
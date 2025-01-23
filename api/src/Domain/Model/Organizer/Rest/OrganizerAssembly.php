<?php

namespace App\Domain\Model\Organizer\Rest;

use App\common\CurrentUser;
use App\common\Rest\Link;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Organizer\Organizer;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class OrganizerAssembly
{

    private $permissinrepository;
    private $settings;
    private EventRepository $eventRepository;


    public function __construct(PermissionRepository $permissionRepository, ContainerInterface $c, EventRepository $eventRepository)
    {
        $this->permissinrepository = $permissionRepository;
        $this->eventRepository = $eventRepository;
        $this->settings = $c->get('settings');
    }


    public function toRepresentations(array $eventsArray): array
    {

        $currentUserUid = CurrentUser::getUser()->getId();

        $permissions = $this->getPermissions($currentUserUid);

        $organizations = array();
        foreach ($eventsArray as $x => $organizer) {
            array_push($organizations, (object)$this->toRepresentation($organizer, $permissions));
        }
        return $organizations;
    }

    public function toRepresentation(Organizer $organizer, array $permissions): OrganizerRepresentation
    {

        $organizationsrepresentation = new OrganizerRepresentation();
        $organizationsrepresentation->setOrganizerId($organizer->getOrganizerId());
        $organizationsrepresentation->setContactPerson($organizer->getContactPerson());
        $organizationsrepresentation->setName($organizer->getName());
        $organizationsrepresentation->setEmail($organizer->getEmail());
        $organizationsrepresentation->setPhone($organizer->getPhone());
        $organizationsrepresentation->setActive($organizer->getActive());

        $events = $this->eventRepository->eventsForOrganizer($organizer->getOrganizerId());


        $linkArray = array();
        foreach ($permissions as $x => $site) {
            if ($site->hasWritePermission()) {
                array_push($linkArray, new Link("relations.organizers", 'POST', $this->settings['path'] . '/organizers'));
                if (count($events) === 0) {
                    array_push($linkArray, new Link("relations.organizers", 'DELETE', $this->settings['path'] . '/organizers'));
                }
                break;
            }
            if ($site->hasReadPermission()) {
                array_push($linkArray, new Link("self", 'GET', $this->settings['path'] . '/organizers/organizer/' . $organizer->getOrganizerId()));
            };
        }
        $organizationsrepresentation->setLinks($linkArray);
        return $organizationsrepresentation;
    }

    public function toOrganizer(OrganizerRepresentation $organizer): Organizer
    {
        return new Organizer("", $organizer->getName(), $organizer->getActive(), false, $organizer->getContactPerson(), $organizer->getEmail(), $organizer->getPhone());

    }

    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("ORGANIZER", CurrentUser::getUser()->getId());

    }

}
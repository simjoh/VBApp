<?php

namespace App\Domain\Model\Organizer\Service;

use App\common\CurrentUser;
use App\common\Exceptions\BrevetException;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Organizer\Organizer;
use App\Domain\Model\Organizer\Repository\OrganizerRepository;
use App\Domain\Model\Organizer\Rest\OrganizerAssembly;
use App\Domain\Model\Organizer\Rest\OrganizerRepresentation;
use App\Domain\Permission\PermissionRepository;
use PhpParser\Node\Expr\Cast\Object_;
use Psr\Container\ContainerInterface;

class OrganizerService extends ServiceAbstract
{

    private PermissionRepository $permissinrepository;
    private OrganizerRepository $organizerRepository;
    private OrganizerAssembly $organizerassembly;
    private EventRepository $eventRepository;


    public function __construct(ContainerInterface   $c,
                                PermissionRepository $permissionRepository,
                                OrganizerRepository  $organizerRepository,
                                OrganizerAssembly    $organizerassembly, EventRepository $eventRepository)
    {
        $this->permissinrepository = $permissionRepository;
        $this->organizerRepository = $organizerRepository;
        $this->organizerassembly = $organizerassembly;
        $this->eventRepository = $eventRepository;
    }

    public function allOrganizers(): array
    {
        $permissions = $this->getPermissions(null);
        if ($this->haspermission($permissions, "READ")) {
            $organizers = $this->organizerRepository->getAll();
            if (!isset($organizers)) {
                return array();
            }
            return $this->organizerassembly->toRepresentations($organizers);
        } else {
            throw new BrevetException("Behörighet saknas", 5, null);
        }
    }


    public function organizer(string $organizer_id): ?OrganizerRepresentation
    {
        if (!isset($organizer_id)) {
            return null;
        }

        $permissions = $this->getPermissions(null);
        if ($this->haspermission($permissions, "READ")) {
            $organizer = $this->organizerRepository->getById($organizer_id);
            return $this->organizerassembly->toRepresentation($organizer, $permissions);
        } else {
            throw new BrevetException("Behörighet saknas", 5, null);
        }
    }

    public function createOrganizer(OrganizerRepresentation $organizer): ?OrganizerRepresentation
    {

        $permissions = $this->getPermissions(null);

        if (!isset($organizer)) {
            throw new BrevetException("Indata saknas", 5, null);
        }

        if ($this->haspermission($permissions, "WRITE")) {
            $organizer = $this->organizerRepository->createOrganizer($this->organizerassembly->toOrganizer($organizer));
            return $this->organizerassembly->toRepresentation($organizer, $permissions);
        } else {
            throw new BrevetException("Behörighet saknas", 5, null);
        }
    }


    public function updateOrganizer(OrganizerRepresentation $organizer): ?OrganizerRepresentation
    {

        if (!isset($organizer)) {
            throw new BrevetException("Indata saknas", 5, null);
        }

        $permissions = $this->getPermissions(null);
        if ($this->haspermission($permissions, "WRITE")) {
            $organizer = $this->organizerRepository->update($this->organizerassembly->toOrganizer($organizer));
            return $this->organizerassembly->toRepresentation($organizer, $permissions);

        } else {
            throw new BrevetException("Behörighet saknas", 5, null);
        }
    }


    public function delete(string $organizer_id): ?OrganizerRepresentation
    {

        $events = $this->eventRepository->eventsForOrganizer($organizer_id);

        if (count($events) > 0) {
            throw new BrevetException("Arrangör kan inta tas bort", 5, null);
        }

        $organizer = $this->organizerRepository->getById($organizer_id);

        if (!isset($organizer)) {
            return null;
        }

        $permissions = $this->getPermissions(null);
        return $this->organizerassembly->toRepresentation($organizer, $permissions);
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("ORGANIZER", CurrentUser::getUser()->getId());

    }
}
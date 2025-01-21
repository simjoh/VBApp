<?php

namespace App\Domain\Model\Organizer\Service;

use App\common\CurrentUser;
use App\common\Service\ServiceAbstract;
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


    public function __construct(ContainerInterface   $c,
                                PermissionRepository $permissionRepository,
                                OrganizerRepository  $organizerRepository,
                                OrganizerAssembly $organizerassembly)
    {
        $this->permissinrepository = $permissionRepository;
        $this->organizerRepository = $organizerRepository;
        $this->organizerassembly = $organizerassembly;
    }

    public function allOrganizers(): array
    {
        $organizers = $this->organizerRepository->getAll();
        if (!isset($organizers)) {
            return array();
        }
        return $this->organizerassembly->toRepresentations($organizers);
    }


    public function organizer(string $organizer_id): ?OrganizerRepresentation
    {
        $organizer = $this->organizerRepository->getById($organizer_id);

        if (!isset($organizer)) {
            return null;
        }
        $permissions = $this->getPermissions(null);
        return $this->organizerassembly->toRepresentation($organizer, $permissions);
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("EVENT", CurrentUser::getUser()->getId());

    }
}
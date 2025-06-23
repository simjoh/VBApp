<?php

namespace App\Domain\Model\Organizer\Service;

use App\Domain\Model\Organizer\Repository\OrganizerRepository;
use App\Domain\Model\Organizer\Rest\OrganizerAssembly;
use App\Domain\Model\Organizer\Rest\OrganizerRepresentation;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class OrganizerService
{
    private $organizerRepository;
    private $permissionRepository;
    private $organizerAssembly;
    private $settings;

    public function __construct(ContainerInterface $c,
                                OrganizerRepository $organizerRepository,
                                PermissionRepository $permissionRepository,
                                OrganizerAssembly $organizerAssembly)
    {
        $this->settings = $c->get('settings');
        $this->organizerRepository = $organizerRepository;
        $this->permissionRepository = $permissionRepository;
        $this->organizerAssembly = $organizerAssembly;
    }

    public function getOrganizerById(int $organizer_id, string $currentuser_id): ?OrganizerRepresentation
    {
        $permissions = $this->getPermissions($currentuser_id);
        $organizer = $this->organizerRepository->getOrganizerById($organizer_id);
        if ($organizer != null) {
            return $this->organizerAssembly->toRepresentation($organizer, $permissions);
        }
        return new OrganizerRepresentation();
    }

    public function getAllOrganizers(string $currentuser_id): ?array
    {
        $permissions = $this->getPermissions($currentuser_id);
        $organizers = $this->organizerRepository->getAllOrganizers();
        return $this->organizerAssembly->toRepresentations($organizers, $currentuser_id);
    }

    public function createOrganizer(string $currentuser_id, OrganizerRepresentation $organizerRepresentation): ?OrganizerRepresentation
    {
        $permissions = $this->getPermissions($currentuser_id);
        
        $organizer = new \App\Domain\Model\Organizer\Organizer(
            0, // ID will be set by database
            $organizerRepresentation->getOrganizationName(),
            $organizerRepresentation->getContactPersonName(),
            $organizerRepresentation->getEmail(),
            $organizerRepresentation->getDescription(),
            $organizerRepresentation->getWebsite(),
            $organizerRepresentation->getWebsitePay(),
            $organizerRepresentation->getLogoSvg(),
            $organizerRepresentation->isActive() ?? true,
            $organizerRepresentation->getClubUid()
        );
        
        $createdOrganizer = $this->organizerRepository->createOrganizer($organizer);
        return $this->organizerAssembly->toRepresentation($createdOrganizer, $permissions);
    }

    public function updateOrganizer(string $currentuser_id, OrganizerRepresentation $organizerRepresentation): ?OrganizerRepresentation
    {
        $organizer = $this->organizerRepository->getOrganizerById($organizerRepresentation->getId());
        if ($organizer == null) {
            return null;
        }
        
        // Update the organizer with new values
        $organizer->setOrganizationName($organizerRepresentation->getOrganizationName());
        $organizer->setDescription($organizerRepresentation->getDescription());
        $organizer->setWebsite($organizerRepresentation->getWebsite());
        $organizer->setWebsitePay($organizerRepresentation->getWebsitePay());
        $organizer->setLogoSvg($organizerRepresentation->getLogoSvg());
        $organizer->setContactPersonName($organizerRepresentation->getContactPersonName());
        $organizer->setEmail($organizerRepresentation->getEmail());
        $organizer->setActive($organizerRepresentation->isActive() ?? true);
        $organizer->setClubUid($organizerRepresentation->getClubUid());
        
        $permissions = $this->getPermissions($currentuser_id);
        $organizerReturn = $this->organizerRepository->updateOrganizer($organizer);
        return $this->organizerAssembly->toRepresentation($organizerReturn, $permissions);
    }

    public function deleteOrganizer(string $currentuser_id, int $organizer_id): bool
    {
        $permissions = $this->getPermissions($currentuser_id);
        return $this->organizerRepository->deleteOrganizer($organizer_id);
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionRepository->getPermissionsTodata("ORGANIZER", $user_uid);
    }
} 
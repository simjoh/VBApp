<?php

namespace App\Domain\Model\Organizer\Rest;

use App\Domain\Model\Organizer\Organizer;
use App\Domain\Permission\PermissionRepository;
use App\common\Rest\Link;
use Psr\Container\ContainerInterface;

class OrganizerAssembly
{
    private $permissinrepository;
    private $settings;

    public function __construct(PermissionRepository $permissionRepository, ContainerInterface $c)
    {
        $this->permissinrepository = $permissionRepository;
        $this->settings = $c->get('settings');
    }

    public function toRepresentations(array $organizersArray, string $currentUserUid): array {
        $permissions = $this->getPermissions($currentUserUid);
        $organizers = array();
        foreach ($organizersArray as $x =>  $organizer) {
            array_push($organizers, (object) $this->toRepresentation($organizer,$permissions));
        }
        return $organizers;
    }

    public function toRepresentation( $organizer,  array $permissions): ?OrganizerRepresentation {
        $organizerrepr = new OrganizerRepresentation();

        $organizerrepr->setId($organizer->getId());
        $organizerrepr->setOrganizationName($organizer->getOrganizationName());
        $organizerrepr->setDescription($organizer->getDescription());
        $organizerrepr->setWebsite($organizer->getWebsite());
        $organizerrepr->setWebsitePay($organizer->getWebsitePay());
        $organizerrepr->setLogoSvg($organizer->getLogoSvg());
        $organizerrepr->setContactPersonName($organizer->getContactPersonName());
        $organizerrepr->setEmail($organizer->getEmail());
        $organizerrepr->setActive($organizer->isActive());
        $organizerrepr->setClubUid($organizer->getClubUid());

        $linkArray = array();

        array_push($linkArray, new Link("self", 'GET', $this->settings['path'] . 'organizer/' . $organizer->getId()));
        array_push($linkArray, new Link("relation.organizer.edit", 'PUT', $this->settings['path'] . 'organizer/' . $organizer->getId()));
        array_push($linkArray, new Link("relation.organizer.delete", 'DELETE', $this->settings['path'] . 'organizer/' . $organizer->getId()));
        
        $organizerrepr->setLinks($linkArray);

        return $organizerrepr;
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("ORGANIZER",$user_uid);
    }
} 
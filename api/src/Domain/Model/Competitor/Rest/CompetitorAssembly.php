<?php

namespace App\Domain\Model\Competitor\Rest;

use App\Domain\Model\Competitor\Competitor;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class CompetitorAssembly
{


    private $permissinrepository;
    private $settings;

    public function __construct(ContainerInterface $c, PermissionRepository $permissionRepository)
    {
        $this->permissinrepository = $permissionRepository;
        $this->settings = $c->get('settings');
    }


    public function toRepresentations(array $eventsArray, string $currentUserUid): array
    {

        $permissions = $this->getPermissions($currentUserUid);

        $competitors = array();
        foreach ($eventsArray as $x => $competitor) {
            array_push($competitors, (object)$this->toRepresentation($competitor, $permissions));
        }
        return $competitors;
    }

    public function toRepresentation(Competitor $competitor, array $permissions): CompetitorRepresentation
    {


        $competitorrepresentation = new CompetitorRepresentation();
        $competitorrepresentation->setGivenName($competitor->getGivenname());
        $competitorrepresentation->setFamilyName($competitor->getFamilyname());
        $competitorrepresentation->setGender($competitor->getGender());
        $competitorrepresentation->setCompetitorUid($competitor->getId());
        $competitorrepresentation->setBirthDate($competitor->getBirthdate() ?? '');
        $competitorrepresentation->setGender($competitor->getGender() ?? '');

        $linkArray = array();
        $competitorrepresentation->setLinks($linkArray);


        return $competitorrepresentation;

    }


    public function getPermissions($user_uid): array
    {
        return $this->permissinrepository->getPermissionsTodata("TRACK", $user_uid);
    }

}
<?php

namespace App\Domain\Model\Site\Service;

use App\common\Exceptions\BrevetException;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\Site\Repository\SiteRepository;
use App\Domain\Model\Site\Rest\SiteAssembly;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Permission\PermissionRepository;

class SiteService extends ServiceAbstract
{
    private $siterepository;
    private $permissionrepository;
    private $siteassembly;

    public function __construct(SiteRepository $siterepository, PermissionRepository $permissionRepository, SiteAssembly $siteAssembly)
    {
        $this->siterepository = $siterepository;
        $this->permissionrepository = $permissionRepository;
        $this->siteassembly = $siteAssembly;

    }

    public function allSites(string $currentuserUid): array
    {
        $permissions = $this->getPermissions($currentuserUid);
        $siteArray = $this->siterepository->allSites();
        return $this->siteassembly->toRepresentations($siteArray, $permissions);
    }

    public function siteFor(string $siteUid, string $currentuserUid): ?SiteRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        $site = $this->siterepository->siteFor($siteUid);
        if (!isset($site)) {
            return null;
        }
        return $this->siteassembly->toRepresentation($site, $permissions);
    }

    public function updateSite(SiteRepresentation $siteRepresentation, string $currentuserUid): SiteRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        $updatedSite = $this->siterepository->updateSite($this->siteassembly->toSite($siteRepresentation));
        return $this->siteassembly->toRepresentation($updatedSite, $permissions);
    }

    public function deleteSite($siteUid)
    {

        if ($this->siterepository->siteInUse($siteUid) == true) {
            throw new BrevetException("Unable to delete site. Site in use", 6, null);
        }


        $this->siterepository->deleteSite($siteUid);
    }

    public function createSite(SiteRepresentation $siteRepresentation)
    {

        $exists = $this->siterepository->existsByPlaceAndAdress2(strtolower(str_replace(' ', '', $siteRepresentation->getPlace())), strtolower(str_replace(' ', '', $siteRepresentation->getAdress())));
        if ($exists !== null) {
            throw new BrevetException("Det finns redan en kontrollplats med samma plats och adress", 6, null);

        }

        return $this->siterepository->createSite($this->siteassembly->toSite($siteRepresentation));

    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("SITE", $user_uid);
    }
}
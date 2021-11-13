<?php

namespace App\Domain\Model\Site\Service;

use App\common\Service\ServiceAbstract;
use App\Domain\Model\Site\Repository\SiteRepository;
use App\Domain\Model\Site\Rest\SiteAssembly;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Permission\PermissionRepository;

class SiteService extends ServiceAbstract
{
    private $siterepository;

    public function __construct(SiteRepository $siterepository, PermissionRepository $permissionRepository, SiteAssembly $siteAssembly)
    {
        $this->siterepository = $siterepository;
        $this->permissionrepository = $permissionRepository;
        $this->siteassembly = $siteAssembly;
    }

    public function allSites(string $currentuserUid): array {
        $permissions = $this->getPermissions($currentuserUid);
        $siteArray = $this->siterepository->allSites();
        return  $this->siteassembly->toRepresentations($siteArray, $permissions);
    }

    public function siteFor(string $siteUid, string $currentuserUid): ?SiteRepresentation {
        $permissions = $this->getPermissions($currentuserUid);
        $site = $this->siterepository->siteFor($siteUid);
        if(!isset($site)){
            return null;
        }
        return    $this->siteassembly->toRepresentation($site, $permissions);
    }

    public function updateSite(SiteRepresentation $siteRepresentation, string $currentuserUid): SiteRepresentation {
        $permissions = $this->getPermissions($currentuserUid);
        $updatedSite = $this->siterepository->updateSite($this->siteassembly->toSite($siteRepresentation));
        return  $this->siteassembly->toRepresentation($updatedSite,$permissions);
    }

    public function deleteSite($siteUid){
        $this->siterepository->deleteSite($siteUid);
    }

    public function createSite(SiteRepresentation $siteRepresentation){
        $this->siterepository->createSite($this->siteassembly->toSite($siteRepresentation));
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("SITE",$user_uid);
    }
}
<?php

namespace App\Domain\Model\Site\Service;

use App\common\Rest\Link;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\Site\Repository\SiteRepository;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Model\Site\Site;
use App\Domain\Permission\PermissionRepository;
use App\Domain\Ping\Service\PingService;
use PrestaShop\Decimal\DecimalNumber;
use Psr\Container\ContainerInterface;

class SiteService extends ServiceAbstract
{
    private $siterepository;

    public function __construct(ContainerInterface $c, SiteRepository $siterepository, PermissionRepository $permissionRepository)
    {
        $this->siterepository = $siterepository;
        $this->permissionrepository = $permissionRepository;
    }

    public function allSites(string $currentuserUid): array {

        $permissions = $this->getPermissions($currentuserUid);

        $siteArray = $this->siterepository->allSites();
        return $this->toRepresentations($siteArray, $permissions);
    }

    public function siteFor(string $siteUid, string $currentuserUid): ?SiteRepresentation {
        $permissions = $this->getPermissions($currentuserUid);
        $site = $this->siterepository->siteFor($siteUid);
        if(!isset($site)){
            return null;
        }
        return   $this->toRepresentation($site, $permissions);
    }

    public function updateSite(SiteRepresentation $siteRepresentation, string $currentuserUid): SiteRepresentation {
        $permissions = $this->getPermissions($currentuserUid);
        $updatedSite = $this->siterepository->updateSite($this->toSite($siteRepresentation));
        return $this->toRepresentation($updatedSite,$permissions);
    }

    public function deleteSite($siteUid){
        $this->siterepository->deleteSite($siteUid);
    }

    public function createSite(SiteRepresentation $siteRepresentation){
        $this->siterepository->createSite($this->toSite($siteRepresentation));
    }


    private function toRepresentations(array $SiteArray, array $permissions): array {
        $siteArray = array();
        foreach ($SiteArray as $x =>  $site) {
            array_push($siteArray, (object) $this->toRepresentation($site, $permissions));
        }
        return $siteArray;
    }
    private function toRepresentation(Site $s, array $permissions): SiteRepresentation {
        $siteRepresentation = new SiteRepresentation();
        $siteRepresentation->setPlace($s->getPlace());
        $siteRepresentation->setSiteUid($s->getSiteUid());
        $siteRepresentation->setLocation($s->getLocation());
        $siteRepresentation->setDescription($s->getDescription());

        $siteRepresentation->setLat($s->getLat()  == null ? "": strval($s->getLat()));
        $siteRepresentation->setLng($s->getLng() == null ? "": strval($s->getLng()));
        // bygg på med lite länkar
        $linkArray = array();
        foreach ($permissions as $x =>  $site) {
            if($site->hasWritePermission()){
                array_push($linkArray, new Link("relation.site.update", 'PUT', '/api/user/' . $s->getSiteUid()));
                array_push($linkArray, new Link("relation.site.delete", 'DELETE', '/api/user/' . $s->getSiteUid()));
                array_push($linkArray, new Link("self", 'GET', '/api/user/' . $s->getSiteUid()));
                break;
            }
            if($site->hasReadPermission()){
                array_push($linkArray, new Link("self", 'GET', '/api/user/' . $s->getSiteUid()));
            };
        }
        $siteRepresentation->setLink($linkArray);
        return $siteRepresentation;
    }

    private function toSite(SiteRepresentation $site){
       return new Site($site->getSiteUid(), $site->getPlace(),
           $site->getAdress(), $site->getDescription()
           ,$site->getLocation(), new DecimalNumber($site->getLat())
           , new DecimalNumber($site->getLng()));
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("SITE",$user_uid);
    }
}
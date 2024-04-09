<?php

namespace App\Domain\Model\Site\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Site\Repository\SiteRepository;
use App\Domain\Model\Site\Site;
use App\Domain\Permission\PermissionRepository;
use PrestaShop\Decimal\DecimalNumber;
use Psr\Container\ContainerInterface;

class SiteAssembly
{

    public function __construct(ContainerInterface $c, PermissionRepository $permissionRepository, SiteRepository $siterepository)
    {
        $this->permissinrepository = $permissionRepository;
        $this->settings = $c->get('settings');
        $this->siterepository = $siterepository;
    }

    public function toRepresentations(array $SiteArray, array $permissions): array
    {
        $siteArray = array();
        foreach ($SiteArray as $x => $site) {
            array_push($siteArray, (object)$this->toRepresentation($site, $permissions));
        }
        return $siteArray;
    }

    public function toRepresentation(Site $s, array $permissions): SiteRepresentation
    {


        $siteRepresentation = new SiteRepresentation();
        $siteRepresentation->setPlace($s->getPlace());
        $siteRepresentation->setSiteUid($s->getSiteUid());

        $siteRepresentation->setAdress($s->getAdress());


        if (!empty($user))
            $siteRepresentation->setLocation(!empty($s->getLocation()) == true ? $s->getLocation() : "");
        $siteRepresentation->setDescription($s->getDescription());
        $siteRepresentation->setImage($s->getPicture() == null ? "" : $this->settings['path'] . "uploads/" . $s->getPicture());

        $siteRepresentation->setLat($s->getLat() == null ? "" : strval($s->getLat()));
        $siteRepresentation->setLng($s->getLng() == null ? "" : strval($s->getLng()));
        // bygg på med lite länkar
        $linkArray = array();
        foreach ($permissions as $x => $site) {
            if ($site->hasWritePermission()) {
                array_push($linkArray, new Link("relation.site.update", 'PUT', $this->settings['path'] . 'site/' . $s->getSiteUid()));
                if ($this->siterepository->siteInUse($s->getSiteUid()) == false) {
                    array_push($linkArray, new Link("relation.site.delete", 'DELETE', $this->settings['path'] . 'site/' . $s->getSiteUid()));
                }

                array_push($linkArray, new Link("self", 'GET', $this->settings['path'] . 'site/' . $s->getSiteUid()));
                break;
            }
            if ($site->hasReadPermission()) {
                array_push($linkArray, new Link("self", 'GET', $this->settings['path'] . 'site/' . $s->getSiteUid()));
            };
        }
        $siteRepresentation->setLink($linkArray);
        return $siteRepresentation;
    }

    public function toSite(SiteRepresentation $site)
    {
        return new Site($site->getSiteUid(), $site->getPlace(),
            $site->getAdress(), $site->getDescription()
            , $site->getLocation(), new DecimalNumber($site->getLat())
            , new DecimalNumber($site->getLng()), substr($site->getImage(), strrpos($site->getImage(), '/') + 1));
    }


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("SITE", $user_uid);
    }
}
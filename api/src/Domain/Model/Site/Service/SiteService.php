<?php

namespace App\Domain\Model\Site\Service;

use App\common\Rest\Link;
use App\Domain\Model\Site\Repository\SiteRepository;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Model\Site\Site;
use App\Domain\Ping\Service\PingService;
use Psr\Container\ContainerInterface;

class SiteService
{
    private $siterepository;

    public function __construct(ContainerInterface $c, SiteRepository $siterepository)
    {
        $this->siterepository = $siterepository;
    }

    public function allSites(): array {
        $siteArray = $this->siterepository->allSites();
        return $this->toRepresentations($siteArray);
    }

    public function siteFor(string $siteUid): ?SiteRepresentation {
      return   $this->toRepresentation($this->siterepository->siteFor($siteUid));
    }

    public function updateSite(SiteRepresentation $siteRepresentation): void {
        $this->siterepository->updateSite($this->toSite($siteRepresentation));
    }

    public function deleteSite($siteUid){
        $this->siterepository->deleteSite($siteUid);
    }

    public function createSite(SiteRepresentation $siteRepresentation){
        $this->siterepository->createSite($this->toSite($siteRepresentation));
    }


    private function toRepresentations(array $SiteArray): array {
        $siteArray = array();
        foreach ($SiteArray as $x =>  $site) {
            array_push($siteArray, (object) $this->toRepresentation($site));
        }
        return $siteArray;
    }
    private function toRepresentation(Site $s): SiteRepresentation {
        $siteRepresentation = new SiteRepresentation();
        $siteRepresentation->setPlace($s->getPlace());
        $siteRepresentation->setSiteUid($s->getSiteUid());
        $siteRepresentation->setLocation($s->getLocation());
        $siteRepresentation->setDescription($s->getDescription());
        // bygg på med lite länkar
        $link = new Link();
        $siteRepresentation->setLink($link);
        return $siteRepresentation;
    }

    private function toSite(SiteRepresentation $site){
       return new Site($site->getSiteUid(), $site->getPlace(), $site->getAdress(), $site->getDescription() ,$site->getLocation());
    }







}
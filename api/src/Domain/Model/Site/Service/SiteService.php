<?php

namespace App\Domain\Model\Site\Service;

use App\common\Rest\Link;
use App\Domain\Model\Site\Repository\SiteRepository;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Model\Site\Site;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

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

    public function siteFor($siteUid ): void {
        $this->siterepository->siteFor($siteUid);
    }

    public function updateSite($siteUid): void {
        $this->siterepository->updateSite($siteUid);
    }

    public function deleteSite($siteUid){
        $this->siterepository->deleteSite($siteUid);
    }

    public function createSite(){
        $this->siterepository->createSite();
    }


    private function toRepresentations(array $SiteArray): array {

        $siteArray = array();
        foreach ($SiteArray as $x =>  $site) {
            array_push($siteArray, (object) $this->toRepresentation($site));
        }
        return $siteArray;
    }

    private function toRepresentation(Site $s): SiteRepresentation {
        $siteRepresentation =  new SiteRepresentation();
        $siteRepresentation->setPlace($s->getPlace());
        $siteRepresentation->setSiteUid($s->getSiteUid());
        $siteRepresentation->setLocation($s->getLocation());
        // bygg på med lite länkar
        $link = new Link();
        $siteRepresentation->setLink($link);
        return $siteRepresentation;
    }







}
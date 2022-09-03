<?php

namespace App\Domain\Model\Track\Rest;

use App\Domain\Model\Event\Event;
use App\Domain\Model\Event\Rest\EventAssembly;
use App\Domain\Model\Site\Service\SiteService;
use App\Domain\Permission\PermissionRepository;

class RusaResponseAssembler
{

    public function __construct(PermissionRepository $permissionRepository, SiteService $siteService, EventAssembly $eventAssembly)
    {
        $this->permissinrepository = $permissionRepository;
        $this->siteservice = $siteService;
        $this->eventassembly = $eventAssembly;
    }




    public function toRepresentation($rusaresponse, string $currentuserUid, RusaPlannerInputRepresentation $rusaPlannnerInput, Event $event): RusaPlannerResponseRepresentation {


        $rusaResponse = new RusaPlannerResponseRepresentation();
        $rusaResponse->setEventRepresentation( $this->eventassembly->toRepresentations(array($event),$currentuserUid)[0]);

        $rusaPlannercontrolls = array();
        if(isset($rusaresponse->CONTROLS) && count($rusaresponse->CONTROLS) > 0){
            foreach ($rusaresponse->CONTROLS as $control) {
                $rusacontrolrepresentation =  $this->createRusaControlResponserepresentation($control);
                $rusaPlannercontroll = new RusaControlResponseRepresentation();
                $siteUid = $this->siteRep($control->CONTROL_DISTANCE_KM,$rusaPlannnerInput->getControls());
                $site =  $this->siteservice->siteFor($siteUid, $currentuserUid);
                $rusaPlannercontroll->setSiteRepresentation($site);
                $rusaPlannercontroll->setRusaControlRepresentation($rusacontrolrepresentation);
                array_push($rusaPlannercontrolls, $rusaPlannercontroll);
            }
            $rusaResponse->setRusaplannercontrols($rusaPlannercontrolls);
        }


        if(isset($rusaresponse->EVENT)){

            $track = $this->rusaTrackrepresentationFrom($rusaresponse->EVENT);
            $track->setTRACKTITLE($rusaPlannnerInput->getTracktitle());
            $track->setLINKTOTRACK($rusaPlannnerInput->getLink());
            $rusaResponse->setRusaTrackRepresentation($track);
        }

        if(isset($rusaresponse->META)){
            $rusaResponse->setRusaMetaRepresentation($this->rusaMetadataRepresentationFrom($rusaresponse->META));
        }




        return $rusaResponse;


    }


    public function createRusaControlResponserepresentation($control): RusaControlRepresentation{
        $rusacontrolrepresentation = new RusaControlRepresentation();
        $rusacontrolrepresentation->setCONTROLDISTANCEKM($control->CONTROL_DISTANCE_KM);
        $rusacontrolrepresentation->setCONTROLDISTANCEMILE($control->CONTROL_DISTANCE_MILE);
        $rusacontrolrepresentation->setOPEN($control->OPEN);
        $rusacontrolrepresentation->setCLOSE($control->CLOSE);
        $rusacontrolrepresentation->setCONTROLNUMBER($control->CONTROL_NUMBER);
        $rusacontrolrepresentation->setCONTROLMETANAME($control->CONTROL_META_NAME);
        $rusacontrolrepresentation->setRELATIVEOPEN($control->RELATIVE_OPEN);
        $rusacontrolrepresentation->setRELATIVECLOSE($control->RELATIVE_CLOSE);
        return $rusacontrolrepresentation;
    }


    private function  rusaMetadataRepresentationFrom(object $metadata): RusaMetaRepresentation{
        $rustametadata = new RusaMetaRepresentation();
        $rustametadata->setAPIVERSION($metadata->API_VERSION);
        $rustametadata->setAPICONTACT($metadata->API_CONTACT);
        $rustametadata->setERROR($metadata->ERROR);
       return  $rustametadata;
}


    public function siteRep($distance, $rusaPlannnerControls): ?string{
        foreach ($rusaPlannnerControls as $key => $value) {
            if ($distance ==  $value->getDISTANCE()) {
              return $value->getSITE();
            }
        }
        return null;
     }

    private function rusaTrackrepresentationFrom($EVENT): RusaTrackRepresentation
    {
        $rusatrack = new RusaTrackRepresentation();
        $rusatrack->setEVENTDISTANCEKM($EVENT->EVENT_DISTANCE_KM);
        $rusatrack->setMAXTIME($EVENT->MAX_TIME);
        $rusatrack->setMINTIME($EVENT->MIN_TIME);
        $rusatrack->setSTARTDATE($EVENT->START_DATE);
        $rusatrack->setSTARTTIME($EVENT->START_TIME);
        return $rusatrack;
    }


}
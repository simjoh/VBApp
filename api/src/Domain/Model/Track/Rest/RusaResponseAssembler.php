<?php

namespace App\Domain\Model\Track\Rest;

use App\Domain\Model\Event\Event;
use App\Domain\Model\Event\Rest\EventAssembly;
use App\Domain\Model\Site\Service\SiteService;
use App\Domain\Permission\PermissionRepository;

class RusaResponseAssembler
{

    private $permissinrepository;
    private $siteservice;
    private $eventassembly;

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

    /**
     * Creates a response specifically from ACP calculator data
     * 
     * @param \App\common\Brevetcalculator\ACPBrevetCalculator $acpCalculator
     * @param array $controlData Array of control data with distance and name information
     * @param RusaPlannerInputRepresentation $rusaPlannnerInput
     * @param string $currentuserUid
     * @param Event $event
     * @return RusaPlannerResponseRepresentation
     */
    public function fromACPCalculator(
        \App\common\Brevetcalculator\ACPBrevetCalculator $acpCalculator,
        array $controlData,
        RusaPlannerInputRepresentation $rusaPlannnerInput,
        string $currentuserUid,
        Event $event
    ): RusaPlannerResponseRepresentation {
        try {
            // Create base response object
            $rusaResponse = new RusaPlannerResponseRepresentation();
            
            // Set event representation
            try {
                $eventRep = $this->eventassembly->toRepresentations(array($event), $currentuserUid);
                if (is_array($eventRep) && count($eventRep) > 0) {
                    $rusaResponse->setEventRepresentation($eventRep[0]);
                }
            } catch (\Exception $e) {
                error_log("Error setting event representation: " . $e->getMessage());
            }
            
            // Create track representation
            try {
                $rusaTrack = new RusaTrackRepresentation();
                $rusaTrack->setEVENTDISTANCEKM((float)$rusaPlannnerInput->getEventDistance());
                $rusaTrack->setMAXTIME($acpCalculator->formatTime($acpCalculator->getMaximumCompletionTime()));
                $rusaTrack->setMINTIME($acpCalculator->formatTime($acpCalculator->getMinimumCompletionTime()));
                $rusaTrack->setSTARTDATE($rusaPlannnerInput->getStartDate() ?? '');
                $rusaTrack->setSTARTTIME($rusaPlannnerInput->getStartTime() ?? '');
                $rusaTrack->setTRACKTITLE($rusaPlannnerInput->getTrackTitle() ?? '');
                $rusaTrack->setLINKTOTRACK($rusaPlannnerInput->getLink() ?? '');
                $rusaResponse->setRusaTrackRepresentation($rusaTrack);
            } catch (\Exception $e) {
                error_log("Error setting track representation: " . $e->getMessage());
            }
            
            // Create meta representation
            try {
                $rustaMeta = new RusaMetaRepresentation();
                $rustaMeta->setAPIVERSION("1.0");
                $rustaMeta->setAPICONTACT("Brevet App ACP Calculator");
                $rustaMeta->setERROR("");
                $rusaResponse->setRusaMetaRepresentation($rustaMeta);
            } catch (\Exception $e) {
                error_log("Error setting meta representation: " . $e->getMessage());
            }
            
            // Create control representations
            try {
                $rusaPlannercontrols = array();
                $controls = $rusaPlannnerInput->getControls();
                
                // Calculate control times for each distance
                $controlDistances = array_map(function($data) {
                    return $data->distance ?? 0; 
                }, $controlData);
                
                $calculatedControls = $acpCalculator->calculateControls($controlDistances);
                
                foreach ($calculatedControls as $index => $controlTimes) {
                    if (!isset($controlDistances[$index])) {
                        continue;
                    }
                    
                    $rusaControlRep = new RusaControlRepresentation();
                    $rusaControlRep->setCONTROLNUMBER($index + 1);
                    $rusaControlRep->setCONTROLDISTANCEKM((float)$controlDistances[$index]);
                    $rusaControlRep->setCONTROLDISTANCEMILE((float)$controlDistances[$index] * 0.621371);
                    
                    // Use ACPBrevetCalculator times for all controls
                    if (isset($controlTimes['opening_datetime']) && $controlTimes['opening_datetime'] instanceof \DateTime) {
                        $rusaControlRep->setOPEN($controlTimes['opening_datetime']->format('Y-m-d H:i'));
                    }
                    if (isset($controlTimes['closing_datetime']) && $controlTimes['closing_datetime'] instanceof \DateTime) {
                        $rusaControlRep->setCLOSE($controlTimes['closing_datetime']->format('Y-m-d H:i'));
                    }
                    if (isset($controlTimes['opening_time'])) {
                        $rusaControlRep->setRELATIVEOPEN($controlTimes['opening_time']);
                    }
                    if (isset($controlTimes['closing_time'])) {
                        $rusaControlRep->setRELATIVECLOSE($controlTimes['closing_time']);
                    }
                    
                    // Set control meta name based on position
                    if ($index === 0) {
                        $rusaControlRep->setCONTROLMETANAME("Start");
                    } else if ($index === count($controlDistances) - 1) {
                        $rusaControlRep->setCONTROLMETANAME("Finish");
                    } else {
                        $rusaControlRep->setCONTROLMETANAME("Control " . $rusaControlRep->getCONTROLNUMBER());
                    }
                    
                    // Create control response representation with site
                    $rusaPlannercontrol = new RusaControlResponseRepresentation();
                    
                    // Get site for this control if available
                    try {
                        $siteUid = null;
                        if (isset($controls[$index]) && method_exists($controls[$index], 'getSITE')) {
                            $siteUid = $controls[$index]->getSITE();
                        }
                        
                        if ($siteUid) {
                            $site = $this->siteservice->siteFor($siteUid, $currentuserUid);
                            if ($site) {
                                $rusaPlannercontrol->setSiteRepresentation($site);
                            }
                        }
                    } catch (\Exception $e) {
                        error_log("Error setting site representation: " . $e->getMessage());
                    }
                    
                    $rusaPlannercontrol->setRusaControlRepresentation($rusaControlRep);
                    $rusaPlannercontrols[] = $rusaPlannercontrol;
                }
                
                $rusaResponse->setRusaplannercontrols($rusaPlannercontrols);
            } catch (\Exception $e) {
                error_log("Error processing controls: " . $e->getMessage());
            }
            
            return $rusaResponse;
        } catch (\Exception $e) {
            error_log("Global error in fromACPCalculator: " . $e->getMessage());
            return new RusaPlannerResponseRepresentation();
        }
    }

}
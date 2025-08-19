<?php

namespace App\Domain\Model\Track\Service;

use App\common\Brevetcalculator\ACPBrevetCalculator;
use App\common\Exceptions\BrevetException;
use App\common\Rest\Client\RusaTimeRestClient;
use App\Domain\Model\Checkpoint\Repository\CheckpointRepository;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Site\Repository\SiteRepository;
use App\Domain\Model\Track\Rest\RusaPlannerInputRepresentation;
use App\Domain\Model\Track\Rest\RusaResponseAssembler;
use Psr\Container\ContainerInterface;
use stdClass;

class RusaTimeTrackPlannerService
{

    private $settings;
    private $siteRepository;
    private $eventRepository;
    private $checkpointRepository;
    private $participantRepository;
    private $rusatimeClient;
    private $rusaresponseAssembler;
    public function __construct(ContainerInterface    $c,
                                SiteRepository        $siteRepository,
                                EventRepository       $eventRepository,
                                CheckpointRepository  $checkpointRepository,
                                ParticipantRepository $participantRepository, RusaTimeRestClient $rusaTimeRestClient, RusaResponseAssembler $rusaResponseAssembler)
    {
        $this->settings = $c->get('settings');
        $this->siteRepository = $siteRepository;
        $this->eventRepository = $eventRepository;
        $this->checkpointRepository = $checkpointRepository;
        $this->participantRepository = $participantRepository;
        $this->rusatimeClient = $rusaTimeRestClient;
        $this->rusaresponseAssembler = $rusaResponseAssembler;
    }

    public function getresponseFromRusaTime(RusaPlannerInputRepresentation $rusaPlannnerInput, string $currentUseruid): object
    {
        $data = new stdClass();;

 

        $event = $this->eventRepository->eventFor($rusaPlannnerInput->getEventUid());
        if ($rusaPlannnerInput->getEventDistance() != "") {

            $payload = $this->prepareEventDistanceParam($rusaPlannnerInput->getEventDistance()) . $this->prepareEventTimePayload($rusaPlannnerInput->getStartDate(), $rusaPlannnerInput->getStartTime()) . '&controls=' . json_encode($this->prepareControlsArray($rusaPlannnerInput->getControls()));

            // lagra namnen på första och sista kontrollen. Apiet kommer alltid att returnera start och finish som namn
            if (count($rusaPlannnerInput->getControls()) > 0) {
                if (count($rusaPlannnerInput->getControls()) > 1) {
                    $lastControl = $this->siteRepository->siteFor($rusaPlannnerInput->getControls()[count($rusaPlannnerInput->getControls()) - 1]->getSITE())->getPlace();
                }
                $firstControl = $this->siteRepository->siteFor($rusaPlannnerInput->getControls()[0]->getSITE())->getPlace();
            }


            $resposne = $this->rusatimeClient->postAsync($payload);
//            print_r($resposne);
            if ($this->isJson($resposne) != true) {
                throw  new BrevetException("No a valid format", 5, null);
            }

            $data = json_decode($resposne);
            $rusacontrols = $data->CONTROLS;

            $modified_controls = array();
            foreach ($rusacontrols as $control) {
                // peta in namnen på första och sista kontrollen
                if (isset($firstControl)) {
                    if ($control->CONTROL_META_NAME == 'START' && $control->CONTROL_NUMBER === 0) {
                        $control->CONTROL_NAME = $firstControl;
                        array_push($modified_controls, $control);
                    }
                }

                if (isset($lastControl)) {
                    if ($control->CONTROL_META_NAME == 'FINISH' && $control->CONTROL_DISTANCE_KM === $data->EVENT->EVENT_DISTANCE_KM) {
                        $control->CONTROL_NAME = $lastControl;
                        if($rusaPlannnerInput->getControls()[count($rusaPlannnerInput->getControls()) - 1]->getDISTANCE() === $data->EVENT->EVENT_DISTANCE_KM) {
                            $control->CONTROL_DISTANCE_KM = $data->EVENT->EVENT_DISTANCE_KM;
                            array_push($modified_controls, $control);
                        } else {
                            $control->CONTROL_DISTANCE_KM = $rusaPlannnerInput->getControls()[count($rusaPlannnerInput->getControls()) - 1]->getDISTANCE();
                            if($control->CONTROL_DISTANCE_KM != $data->EVENT->EVENT_DISTANCE_KM){
                               // $control->OPEN = null;
                            }
                            array_push($modified_controls, $control);
                        }
                    }
                }

                if ($control->CONTROL_META_NAME != 'FINISH' && $control->CONTROL_META_NAME != 'START') {
                    array_push($modified_controls, $control);
                }
            }
        }

        if(isset($modified_controls) && count($modified_controls) > 0){
            $data->CONTROLS = $modified_controls;
        }


        if(count($rusaPlannnerInput->getControls()) === 0){
            $data->CONTROLS = null;
        }

        $resposne = null;

        if(isset($event)){
            $resposne = $this->rusaresponseAssembler->toRepresentation($data, $currentUseruid, $rusaPlannnerInput,$event);
        }


        if($resposne == null){
            return new stdClass();
        }

        return $resposne;
       // return $data;
    }


    private function prepareEventDistanceParam(string $event_distance)
    {
        if (isset($event_distance)) {
            return "?event_distance=" . $event_distance . "km";
        } else {
            return "";
        }

    }


    private function prepareEventTimePayload(string $startdate, string $starttime)
    {

        $startimePayload = "";

        if ($startdate != null && $startdate != "") {
            $startimePayload = $startimePayload . "&start_date=" . $startdate;
        }
        if ($starttime != null && $starttime != "") {
            $startimePayload = $startimePayload . "&start_time=" . $starttime;
        }

        if ($startimePayload === null) {
            return null;
        } else {
            return $startimePayload;
        }
    }

    private function prepareControlsArray(array $controls): array
    {
        $returarray = array();
        foreach ($controls as $key => $value) {
            if ($key != 0 && $key != count($controls) - 1) {
                $w = new test();
                $site = $this->siteRepository->siteFor($value->getSITE());
                $w->NAME = $site->getPlace();
                $w->DISTANCE = $value->getDISTANCE() . 'km';
                array_push($returarray, $w);
            }
        }

        return $returarray;
    }

    function isJson($string)
    {
        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Calculate control times using ACP Brevet Calculator instead of RUSA Time API
     * 
     * @param RusaPlannerInputRepresentation $rusaPlannnerInput Input data 
     * @param string $currentUseruid Current user's UID
     * @return \App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation Response with calculated control times
     */
    public function getResponseFromACPCalculator(RusaPlannerInputRepresentation $rusaPlannnerInput, string $currentUseruid): \App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation
    {
        try {
            // Validate input
            if (!$rusaPlannnerInput) {
                return new \App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation();
            }
            
            // Get the event distance
            $eventDistance = $rusaPlannnerInput->getEventDistance();
            if (!$eventDistance) {
                return new \App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation();
            }
            
            // Get the start time
            $startDate = $rusaPlannnerInput->getStartDate();
            $startTime = $rusaPlannnerInput->getStartTime();
            
            if (!$startDate || !$startTime) {
                return new \App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation();
            }
            
            // Create datetime for start
            $startDateTime = $startDate . ' ' . $startTime;
            
            // Initialize ACP calculator
            $acpCalculator = new \App\common\Brevetcalculator\ACPBrevetCalculator($eventDistance, $startDateTime);
            
            // Get controls from input
            $controls = $rusaPlannnerInput->getControls();
            if (count($controls) === 0) {
                return new \App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation();
            }
            
            // Create an array with control information including distances
            $controlData = [];
            foreach ($controls as $index => $control) {
                $data = new \stdClass();
                $data->original_index = $index;
                $data->control = $control;
                
                // Get control distance
                if (method_exists($control, 'getDistance')) {
                    $data->distance = $control->getDistance();
                } else if (property_exists($control, 'distance')) {
                    $data->distance = $control->distance;
                } else if (property_exists($control, 'DISTANCE')) {
                    $data->distance = $control->DISTANCE;
                } else if (method_exists($control, 'getDISTANCE')) {
                    $data->distance = $control->getDISTANCE();
                } else {
                    // Default to 0 if no distance found
                    $data->distance = 0;
                }
                
                // Get control site information if available
                if (method_exists($control, 'getSITE')) {
                    try {
                        $site = $this->siteRepository->siteFor($control->getSITE());
                        if ($site) {
                            $data->name = $site->getPlace();
                        }
                    } catch (\Exception $e) {
                        // Handle exception
                    }
                }
                
                $controlData[] = $data;
            }
            
            // Sort controls by distance (ascending)
            usort($controlData, function($a, $b) {
                return $a->distance <=> $b->distance;
            });
            
            // Get event from repository if needed
            $event = null;
            try {
                if ($rusaPlannnerInput->getEventUid()) {
                    $event = $this->eventRepository->eventFor($rusaPlannnerInput->getEventUid());
                    
                    // Use the dedicated assembler method if we have an event
                    if ($event) {
                        // Try to use the new method
                        try {
                            return $this->rusaresponseAssembler->fromACPCalculator(
                                $acpCalculator,
                                $controlData,
                                $rusaPlannnerInput,
                                $currentUseruid,
                                $event
                            );
                        } catch (\Exception $innerEx) {
                            // Fall back to original method if the new one fails
                            
                            // Build a simple response like before
                            $response = new \stdClass();
                            $response->EVENT = new \stdClass();
                            $response->EVENT->EVENT_DISTANCE_KM = (float)$eventDistance;
                            $response->EVENT->MAX_TIME = $acpCalculator->formatTime($acpCalculator->getMaximumCompletionTime());
                            $response->EVENT->MIN_TIME = $acpCalculator->formatTime($acpCalculator->getMinimumCompletionTime());
                            
                            // Try to use the original toRepresentation method
                            try {
                                return $this->rusaresponseAssembler->toRepresentation($response, $currentUseruid, $rusaPlannnerInput, $event);
                            } catch (\Exception $e2) {
                                return new \App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation();
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                // Error getting event
            }
            
            // If we couldn't get the event or there was an error, return an empty response
            return new \App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation();
        } catch (\Exception $e) {
            return new \App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation();
        }
    }

}

class test
{
    public $NAME;
    public $DISTANCE;
}
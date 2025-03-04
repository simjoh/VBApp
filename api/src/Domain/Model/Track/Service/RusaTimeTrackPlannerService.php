<?php

namespace App\Domain\Model\Track\Service;

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


}

class test
{
    public $NAME;
    public $DISTANCE;
}
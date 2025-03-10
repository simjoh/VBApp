<?php

namespace App\Domain\Model\Track\Service;

use App\common\Brevetcalculator\ACPBrevetCalculator;
use App\common\Exceptions\BrevetException;
use App\Domain\Model\Checkpoint\Repository\CheckpointRepository;
use App\Domain\Model\Event\Event;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Site\Repository\SiteRepository;
use App\Domain\Model\Track\Rest\RusaControlRepresentation;
use App\Domain\Model\Track\Rest\RusaControlResponseRepresentation;
use App\Domain\Model\Track\Rest\RusaMetaRepresentation;
use App\Domain\Model\Track\Rest\RusaPlannerInputRepresentation;
use App\Domain\Model\Track\Rest\RusaPlannerResponseRepresentation;
use App\Domain\Model\Track\Rest\RusaResponseAssembler;
use App\Domain\Model\Track\Rest\RusaTrackRepresentation;
use DateTime;
use Psr\Container\ContainerInterface;
use stdClass;

class ACPBrevetTrackPlannerService
{
    private $settings;
    private $siteRepository;
    private $eventRepository;
    private $checkpointRepository;
    private $participantRepository;
    private $rusaResponseAssembler;

    public function __construct(
        ContainerInterface $c,
        SiteRepository $siteRepository,
        EventRepository $eventRepository,
        CheckpointRepository $checkpointRepository,
        ParticipantRepository $participantRepository,
        RusaResponseAssembler $rusaResponseAssembler
    ) {
        $this->settings = $c->get('settings');
        $this->siteRepository = $siteRepository;
        $this->eventRepository = $eventRepository;
        $this->checkpointRepository = $checkpointRepository;
        $this->participantRepository = $participantRepository;
        $this->rusaResponseAssembler = $rusaResponseAssembler;
    }

    public function getresponseFromACPBrevet(RusaPlannerInputRepresentation $rusaPlannnerInput, string $currentUseruid): object
    {
        $data = new stdClass();

        // Get the event and validate the required input
        $event = $this->eventRepository->eventFor($rusaPlannnerInput->getEventUid());
        if ($rusaPlannnerInput->getEventDistance() == "") {
            return new stdClass();
        }

        // Create an ACP Brevet Calculator instance
        $startDateTime = $rusaPlannnerInput->getStartDate() . ' ' . $rusaPlannnerInput->getStartTime();
        $distance = floatval($rusaPlannnerInput->getEventDistance());
        $calculator = new ACPBrevetCalculator($distance, $startDateTime);
        
        // Process the event data
        $eventObj = $this->createEventObject($calculator, $rusaPlannnerInput);
        $data->EVENT = $eventObj;
        
        // Process controls data
        $controlsArray = [];
        if (count($rusaPlannnerInput->getControls()) > 0) {
            $controlsArray = $this->processControls($calculator, $rusaPlannnerInput);
        }
        $data->CONTROLS = $controlsArray;
        
        // Set metadata
        $data->META = $this->createMetaObject();

        // Transform the response using the existing assembler
        $response = null;
        if (isset($event)) {
            $response = $this->rusaResponseAssembler->toRepresentation($data, $currentUseruid, $rusaPlannnerInput, $event);
        }

        if ($response == null) {
            return new stdClass();
        }

        return $response;
    }

    private function createEventObject(ACPBrevetCalculator $calculator, RusaPlannerInputRepresentation $input): object
    {
        $event = new stdClass();
        $event->EVENT_DISTANCE_KM = floatval($input->getEventDistance());
        $event->EVENT_DISTANCE_MILE = round(floatval($input->getEventDistance()) * 0.621371, 1);
        $event->START_DATE = $input->getStartDate();
        $event->START_TIME = $input->getStartTime();
        
        // Get minimum and maximum completion times
        $minTimeHours = $calculator->getMinimumCompletionTime();
        $maxTimeHours = $calculator->getMaximumCompletionTime();
        
        // Format times for display
        $event->MIN_TIME = $calculator->formatTime($minTimeHours);
        $event->MAX_TIME = $calculator->formatTime($maxTimeHours);
        
        // Identify this as using the ACP calculator
        $event->CALC_METHOD = "ACP Brevet Calculator";
        
        return $event;
    }

    private function processControls(ACPBrevetCalculator $calculator, RusaPlannerInputRepresentation $input): array
    {
        $controls = [];
        $controlIndex = 0;
        
        foreach ($input->getControls() as $control) {
            $controlObj = new stdClass();
            $distance = floatval($control->getDISTANCE());
            
            // Set control metadata
            $controlObj->CONTROL_NUMBER = $controlIndex;
            $controlObj->CONTROL_DISTANCE_KM = $distance;
            $controlObj->CONTROL_DISTANCE_MILE = round($distance * 0.621371, 1);
            
            // Get site information
            $site = $this->siteRepository->siteFor($control->getSITE());
            $controlObj->CONTROL_NAME = $site->getPlace();
            
            // Determine control type (START, CONTROL, FINISH)
            if ($controlIndex === 0) {
                $controlObj->CONTROL_META_NAME = 'START';
            } elseif ($controlIndex === count($input->getControls()) - 1) {
                $controlObj->CONTROL_META_NAME = 'FINISH';
            } else {
                $controlObj->CONTROL_META_NAME = 'CONTROL';
            }
            
            // Calculate opening and closing times
            $openDateTime = $calculator->getOpeningDateTime($distance);
            $closeDateTime = $calculator->getClosingDateTime($distance);
            
            // Format dates for output
            $controlObj->OPEN = $openDateTime->format('Y-m-d H:i:s');
            $controlObj->CLOSE = $closeDateTime->format('Y-m-d H:i:s');
            
            // Calculate relative times (in hours and minutes format)
            $controlObj->RELATIVE_OPEN = $calculator->formatTime($calculator->calculateOpeningTime($distance));
            $controlObj->RELATIVE_CLOSE = $calculator->formatTime($calculator->calculateClosingTime($distance));
            
            $controls[] = $controlObj;
            $controlIndex++;
        }
        
        return $controls;
    }

    private function createMetaObject(): object
    {
        $meta = new stdClass();
        $meta->API_VERSION = "1.0";
        $meta->API_CONTACT = "Audax Club Parisien (ACP) Brevet Calculator";
        $meta->ERROR = null;
        
        return $meta;
    }
} 
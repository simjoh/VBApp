<?php

namespace App\Domain\Model\Track\Service;


use App\common\Service\ServiceAbstract;
use App\Domain\Model\CheckPoint\Checkpoint;
use App\Domain\Model\Checkpoint\Repository\CheckpointRepository;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Event\Event;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Event\Service\EventService;
use App\Domain\Model\Site\Repository\SiteRepository;
use App\Domain\Model\Site\Site;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Model\Track\Rest\TrackAssembly;
use App\Domain\Model\Track\Rest\TrackRepresentation;
use App\Domain\Model\Track\Track;
use App\Domain\Model\Track\TracksForEvents;
use App\Domain\Permission\PermissionRepository;
use Cassandra\Set;
use Equip\Structure\Dictionary;
use Equip\Structure\UnorderedList;
use Iterator;
use League\Csv\Reader;
use League\Csv\ResultSet;
use League\Csv\Statement;
use Nette\Utils\ArrayHash;
use Nette\Utils\ArrayList;
use PrestaShop\Decimal\DecimalNumber;
use Psr\Container\ContainerInterface;
use Ramsey\Uuid\Uuid;

class TrackService extends ServiceAbstract
{

    private $trackRepository;
    private $checkpointService;

    public function __construct(ContainerInterface $c ,
                                TrackRepository $trackRepository,
                                CheckpointsService $checkpointService,
                                PermissionRepository $permissionRepository,
                                TrackAssembly $trackAssembly,
                                SiteRepository $siteRepository,
                                EventRepository $eventRepository,
                                CheckpointRepository $checkpointRepository)
    {
        $this->trackRepository = $trackRepository;
       $this->checkpointService = $checkpointService;
        $this->permissionrepository = $permissionRepository;
        $this->trackAssembly = $trackAssembly;
        $this->settings = $c->get('settings');
        $this->siteRepository = $siteRepository;
        $this->eventRepository = $eventRepository;
        $this->checkpointRepository = $checkpointRepository;
    }

    public function allTracks(string $currentuserUid): array {
        $permissions = $this->getPermissions($currentuserUid);
        $trackArray = $this->trackRepository->allTracks();
        // hämta checkpoints
       return $this->trackAssembly->toRepresentations($trackArray, $currentuserUid, $permissions);
    }

    public function getTrackByTrackUid(string $trackUid, string $currentuserUid) : TrackRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        $track = $this->trackRepository->getTrackByUid($trackUid);
        return $this->trackAssembly->toRepresentation($track, $permissions, $currentuserUid);
    }

    public function updateTrack(TrackRepresentation $trackrepresentation,  string $currentuserUid): ?TrackRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        if(!empty($trackrepresentation)){
              $trackUpdated = $this->trackRepository->updateTrack($this->trackAssembly->totrack($trackrepresentation));
              return $this->trackAssembly->toRepresentation($trackUpdated, $permissions, $currentuserUid);
        }
        return null;

    }

    public function tracksForEvent(mixed $currentuserUid, string $event_uid): ?array
    {
        $permissions = $this->getPermissions($currentuserUid);
       $track_uids = $this->eventRepository->tracksOnEvent($event_uid);
       if(empty($track_uids)){
           return array();
       }
          $tracks =$this->trackRepository->tracksOnEvent($track_uids);
         return $this->trackAssembly->toRepresentations($tracks, $permissions, $currentuserUid);
    }

    public function createTrack(TrackRepresentation $trackrepresentation,string $currentuserUid): TrackRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        $createdTrack = $this->trackRepository->createTrack($this->trackAssembly->totrack($trackrepresentation));
        return $this->trackAssembly->toRepresentation($createdTrack, $permissions, $currentuserUid);
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("TRACK",$user_uid);
    }

    public function buildFromCsv(?string $filename, string $uploadDir, string $getAttribute)
    {
        // Lös in filen som laddades upp
        $csv = Reader::createFromPath($this->settings['upload_directory']  . 'banor2022.csv', 'r');
        $csv->setDelimiter(";");
        $csv->skipEmptyRecords();
        $csv->skipInputBOM();

        $records = $csv->getRecords();


        $stmt = Statement::create()
            ->offset(0);

        //query your records from the document
        $recordss= $stmt->process($csv)->getRecords();
        $recordsss= $stmt->process($csv)->getRecords();


        $eachEvent = new Dictionary();
        // Vilka event har vi
        foreach ($records  as $rowIndex => $record) {
            if (!$eachEvent->hasValue($record[0])) {
                $eachEvent = $eachEvent->withValue($record[0], array());
            }
        }

        $rader = [];
        foreach ($recordss  as $rowIndex => $record22) {
            if ($eachEvent->hasValue($record22[0])) {
                array_push($rader, $record22);
            }
        }

        // raderna grupperat på Event
        foreach ($recordsss  as $rowIndex => $record23) {
            $dns = [];
            if ($eachEvent->hasValue($record23[0])) {
                foreach ($rader  as $indx => $ee) {
                    if($ee[0] == $record23[0]){
                        array_push($dns, $ee);
                    }
                }
                $eachEvent =   $eachEvent->withValue($record23[0], $dns);
            }
        }
        // skapa banor osv
        foreach ($eachEvent as $key => $value) {
            $track = new TracksForEvents();
            if($eachEvent->hasValue($key)){
                $rad = $eachEvent->getValue($key);
                   $track->event = $this->createEvent($rad);
                   $track->sites =   $this->createSite($rad);
                   $track->checkpoints = $this->createControl($rad, $track);
                   $track->track = $this->buildTrackFromCsv($track,$rad);
                   if($track->track == null){
                       $this->createTracksOnEvent($track->track, $track->event);
                   }
            }
        }
    }



    private function createSite(array $record): ?array {

        $siteDict = new Dictionary();
        $sitereturn = [];
        foreach ($record as $key => $value) {
                if(!$siteDict->hasValue($value[4] .  $value[5])){
                    $siteDict = $siteDict->withValue($value[4] .  $value[5] , new Site("", $value[4],$value[5], $value[12], null,
                        empty($value[7]) ? new DecimalNumber("0")  : new DecimalNumber(strval($value[7])),
                        empty($value[8]) ? new DecimalNumber("0")  : new DecimalNumber(strval($value[8])),
                        "IMG.JPG"));
                    $existingSite = $this->siteRepository->existsByPlaceAndAdress($value[4],$value[5]);
                    if($existingSite == null){
                        array_push($sitereturn,$this->siteRepository->createSite($siteDict->getValue($value[4] .  $value[5])));
                    } else {
                        array_push($sitereturn, $existingSite);
                    }
                }
        }
        return $sitereturn;


    }

    private function createEvent(array $record): ?Event
    {
        $events = "";
        $event = new Event();
        foreach ($record as $key => $value) {
            if($events == ""){
                $event->setTitle($value[0]);
                $event->setActive(false);
                $event->setCanceled(false);
                $event->setCompleted(false);
                $event->setStartdate($value[2]);
                $event->setEnddate($value[2]);
            $existsing =  $this->eventRepository->existsByTitleAndStartDate($value[0],$value[2]);
            if($existsing == null){
                return $this->eventRepository->createEvent($event);
            } else {
                return $existsing;
            }

            }
        }
        return null;

    }

    private function createControl(array $rad, TracksForEvents $track): array
    {
        $checkpoints = [];
        foreach ($rad as $key => $value){
            $checkpoint = new Checkpoint();
            $sites = $track->sites;
            foreach ($sites  as $rowIndex => $record) {
                $place = $value[4];
                $adress = $value[5];
                if($place == $record->getPlace() && $adress == $record->getAdress()){
                    $checkpoint->setSiteUid($record->getSiteUid());
                }
            }
            $checkpoint->setOpens($value[10] != null ? $value[10] : null);
            $checkpoint->setClosing($value[11] != null ? $value[11] : null);
            $checkpoint->setDistance(floatval($value[9]));

           $sss = $this->checkpointRepository->existsBySiteUidAndDistance($checkpoint->getSiteUid(), $checkpoint->getDistance());
           if($sss == null){
              $this->checkpointRepository->createCheckpoint(null, $checkpoint);
               array_push($checkpoints, $checkpoint);
           } else {
               array_push($checkpoints, $sss);
           }


        }
        return $checkpoints;

    }

    private function buildTrackFromCsv(TracksForEvents $track, array $rad): Track
    {
        $trackToCreate = new Track();
        $trackToCreate->setDescription("");
        $trackToCreate->setTitle($rad[0][1]);
        $trackToCreate->setLink("gggddd");
        $trackToCreate->setHeightdifference(2000);
        $trackToCreate->setDistance(200);
        $trackToCreate->setEventUid($track->event->getEventUid());
        if($track->checkpoints !== null){
            $checkpoints = $track->checkpoints;
            if(!empty($checkpoints)){
                $checkpoints_uid = [];
                foreach ($checkpoints as $chp => $checkpoint){
                   array_push($checkpoints_uid, $checkpoint->getCheckpointUid());
                }
              $trackToCreate->setCheckpoints($checkpoints_uid);
            }
        }
        $track = $this->trackRepository->trackAndCheckpointsExists($trackToCreate->getEventUid(), $trackToCreate->getTitle(), $trackToCreate->getDistance(), $trackToCreate->getCheckpoints());
        if($track){
            return $track;
        }
       return $this->trackRepository->createTrack($trackToCreate);

    }

    private function createTracksOnEvent(Track $track, Event $event)
    {
        $track_uids = [];
        array_push($track_uids, $track->getTrackUid());
        $this->eventRepository->createTrackEvent($event->getEventUid(), $track_uids);

    }




}
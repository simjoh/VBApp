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

    public function tracksForEvent(string $currentuserUid, string $event_uid): ?array
    {
        $permissions = $this->getPermissions($currentuserUid);
       $track_uids = $this->eventRepository->tracksOnEvent($event_uid);
       if(empty($track_uids)){
           return array();
       }
        $test = [];
        foreach ($track_uids as $s => $ro){
            $test[] = $ro[$s];
        }
          $tracks = $this->trackRepository->tracksOnEvent($test);
         return $this->trackAssembly->toRepresentations($tracks,  $currentuserUid,$permissions);
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
        $eachEvent2 = new Dictionary();

        foreach ($records  as $rowIndex => $record) {
            if (!$eachEvent2->hasValue($record[0])) {
                $eachEvent2 = $eachEvent2->withValue($record[0], new Dictionary());
            }
        }

        $rader = [];
        foreach ($recordss  as $rowIndex => $record22) {
                array_push($rader, $record22);
        }


        foreach ($recordsss  as $rowIndex => $record23) {
                $track = $eachEvent2->getValue($record23[0]);
               $dns = [];
                foreach ($rader  as $indx => $ee) {
                    if($ee[1] . $ee[2] . $ee[3]  == $record23[1] . $record23[2] . $record23[3]){
                        array_push($dns, $ee);
                    }
                }
                $track = $track->withValue($record23[1] . $record23[2] . $record23[3], $dns);
                $eachEvent2 = $eachEvent2->withValue($record23[0], $track);
        }


        // skapa banor osv
        foreach ($eachEvent2 as $key => $value) {
            $track = new TracksForEvents();
            if($eachEvent2->hasValue($key)){
                $track_uids = [];
                if(!empty($value)){
                    $rad = $eachEvent2->getValue($key);
                    $track->event = $this->createEvent($rad);
                    $track->sites =   $this->createSite($rad);
                    $track->checkpoints = $this->createControl($rad, $track);
                    $track->track = $this->buildTrackFromCsv($track,$rad);
                }
                   if($track->track != null){
                       array_push($track_uids, $track->track->getTrackUid());
                       $this->createTracksOnEvent($track->track, $track->event, $track_uids);
                   }
            }
        }
    }



    private function createSite( $record): ?array {

        $siteDict = new Dictionary();
        $sitereturn = [];
        foreach ($record as $key => $value) {
            foreach ($value as $key => $row) {

                if(!$siteDict->hasValue($row[4] .  $row[5])){
                    $siteDict = $siteDict->withValue($row[4] .  $row[5] , new Site("", $row[4],$row[5], $row[12], null,
                        empty($row[7]) ? new DecimalNumber("0")  : new DecimalNumber(strval($row[7])),
                        empty($row[8]) ? new DecimalNumber("0")  : new DecimalNumber(strval($row[8])),
                        "IMG.JPG"));
                    $existingSite = $this->siteRepository->existsByPlaceAndAdress($row[4],$row[5]);
                    if($existingSite == null){
                        array_push($sitereturn,$this->siteRepository->createSite($siteDict->getValue($row[4] .  $row[5])));
                    } else {
                        array_push($sitereturn, $existingSite);
                    }
                }
            }
        }
        return $sitereturn;


    }

    private function createEvent($record): ?Event
    {

        $events = "";
        $event = new Event();
        foreach ($record as $key => $value) {
            foreach ($value as $key => $row){
                if($events == ""){
                    $event->setTitle($row[0]);
                    $event->setActive(false);
                    $event->setCanceled(false);
                    $event->setCompleted(false);
                    $event->setStartdate($row[2]);
                    $event->setEnddate($row[2]);
                    $existsing =  $this->eventRepository->existsByTitleAndStartDate($row[0],$row[2]);
                    if($existsing == null){
                        return $this->eventRepository->createEvent($event);
                    } else {
                        return $existsing;
                    }
                }
            }

        }
        return null;

    }

    private function createControl( $rad, TracksForEvents $track): array
    {
        $checkpoints = [];
        foreach ($rad as $key => $value){
            foreach ($value as $key => $row) {
                $checkpoint = new Checkpoint();
                $sites = $track->sites;
                foreach ($sites as $rowIndex => $record) {
                    $place = $row[4];
                    $adress = $row[5];
                    if ($place == $record->getPlace() && $adress == $record->getAdress()) {
                        $checkpoint->setSiteUid($record->getSiteUid());
                    }
                }
                $checkpoint->setOpens($row[10] != null ? $row[10] : null);
                $checkpoint->setClosing($row[11] != null ? $row[11] : null);
                $checkpoint->setDistance(floatval($row[9]));

                $sss = $this->checkpointRepository->existsBySiteUidAndDistance($checkpoint->getSiteUid(), $checkpoint->getDistance(), $checkpoint->getOpens(), $checkpoint->getClosing());
                if ($sss == null) {
                    $this->checkpointRepository->createCheckpoint(null, $checkpoint);
                    array_push($checkpoints, $checkpoint);
                } else {
                 //   array_push($checkpoints, $sss);
                }
            }

        }
        return $checkpoints;

    }

    private function buildTrackFromCsv(TracksForEvents $trackin,  $rad): ?Track
    {

        $noMoreCheckpointiftrue = false;
        foreach ($rad as $key => $value) {
            $trackToCreate = new Track();
            $trackToCreate->setDescription("");
            $trackToCreate->setTitle($value[0][1]);
            $trackToCreate->setLink("http://www.banan.on.strava");
            $trackToCreate->setHeightdifference(2000);
            $trackToCreate->setDistance(200);
            $trackToCreate->setStartDateTime($value[0][10]);
            $existintevent =  $this->eventRepository->eventFor($trackin->event->getEventUid());
            if(isset($existintevent)){
                $trackToCreate->setEventUid($trackin->event->getEventUid());
            } else {
            }

            if ($trackin->checkpoints !== null) {
                $checkpoints = $trackin->checkpoints;
                if (!empty($checkpoints)) {
                    if($noMoreCheckpointiftrue == false){
                        $noMoreCheckpointiftrue = true;
                      $checkpoints_uid = [];
                    foreach ($checkpoints as $chp => $checkpoint) {
                        array_push($checkpoints_uid, $checkpoint->getCheckpointUid());
                    }
                    $trackToCreate->setCheckpoints($checkpoints_uid);
                    }
                }
            }
            $trackwithstartdate = $this->trackRepository->trackWithStartdateExists($trackToCreate->getEventUid(), $trackToCreate->getTitle(), $value[0][10]);;
            if ($trackwithstartdate != null) {
                return $trackwithstartdate;
            }
                $track = $this->trackRepository->trackAndCheckpointsExists($trackToCreate->getEventUid(), $trackToCreate->getTitle(), $trackToCreate->getDistance(), $trackToCreate->getCheckpoints());

            if ($track) {
                return $track;
            }
             $this->trackRepository->createTrack($trackToCreate);


        }

            return new Track();

    }

    private function createTracksOnEvent(Track $track, Event $event, array $track_uids)
    {

        $tracksOnEvent = $this->eventRepository->tracksOnEvent($event->getEventUid());
        if(empty($tracksOnEvent)){
            $this->eventRepository->createTrackEvent($event->getEventUid(), $track_uids);
        }
    }




}
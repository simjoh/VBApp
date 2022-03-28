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
use App\Domain\Model\Track\Bana;
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

    public function __construct(ContainerInterface   $c,
                                TrackRepository      $trackRepository,
                                CheckpointsService   $checkpointService,
                                PermissionRepository $permissionRepository,
                                TrackAssembly        $trackAssembly,
                                SiteRepository       $siteRepository,
                                EventRepository      $eventRepository,
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

    public function allTracks(string $currentuserUid): array
    {
        $permissions = $this->getPermissions($currentuserUid);
        $trackArray = $this->trackRepository->allTracks();
        // hämta checkpoints
        return $this->trackAssembly->toRepresentations($trackArray, $currentuserUid, $permissions);
    }

    public function getTrackByTrackUid(string $trackUid, string $currentuserUid): TrackRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        $track = $this->trackRepository->getTrackByUid($trackUid);
        return $this->trackAssembly->toRepresentation($track, $permissions, $currentuserUid);
    }

    public function updateTrack(TrackRepresentation $trackrepresentation, string $currentuserUid): ?TrackRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        if (!empty($trackrepresentation)) {
            $trackUpdated = $this->trackRepository->updateTrack($this->trackAssembly->totrack($trackrepresentation));
            return $this->trackAssembly->toRepresentation($trackUpdated, $permissions, $currentuserUid);
        }
        return null;

    }

    public function tracksForEvent(string $currentuserUid, string $event_uid): ?array
    {
        $permissions = $this->getPermissions($currentuserUid);
        $tracks = $this->trackRepository->tracksbyEvent($event_uid);

//        if (empty($track_uids)) {
//            return array();
//        }
//        $test = [];
//        foreach ($track_uids as $s => $ro) {
//            $test[] = $ro[$s];
//        }
//        $tracks = $this->trackRepository->tracksOnEvent($test);

        return $this->trackAssembly->toRepresentations($tracks, $currentuserUid, $permissions);
    }

    public function createTrack(TrackRepresentation $trackrepresentation, string $currentuserUid): TrackRepresentation
    {
        $permissions = $this->getPermissions($currentuserUid);
        $createdTrack = $this->trackRepository->createTrack($this->trackAssembly->totrack($trackrepresentation));
        return $this->trackAssembly->toRepresentation($createdTrack, $permissions, $currentuserUid);
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("TRACK", $user_uid);
    }

    public function buildFromCsv(?string $filename, string $uploadDir, string $getAttribute)
    {
        // Lös in filen som laddades upp
        $csv = Reader::createFromPath($this->settings['upload_directory'] . $filename, 'r');
        $csv->setDelimiter(";");
        $csv->skipEmptyRecords();
        $csv->skipInputBOM();

        $records = $csv->getRecords();


        $stmt = Statement::create()
            ->offset(0);

        //query your records from the document
        $recordss = $stmt->process($csv)->getRecords();
        $recordsss = $stmt->process($csv)->getRecords();


        $eachEvent = new Dictionary();
        $eachEvent2 = new Dictionary();

        foreach ($records as $rowIndex => $record) {
            if (!$eachEvent2->hasValue($record[0])) {
                $eachEvent2 = $eachEvent2->withValue($record[0], new Dictionary());
            }
        }

        $rader = [];
        foreach ($recordss as $rowIndex => $record22) {
            array_push($rader, $record22);
        }


        foreach ($recordsss as $rowIndex => $record23) {
            $track = $eachEvent2->getValue($record23[0]);
            $dns = [];
            foreach ($rader as $indx => $ee) {
                if ($ee[1] . $ee[3] . $ee[4] == $record23[1] . $record23[3] . $record23[4]) {
                    array_push($dns, $ee);
                }
            }
            $track = $track->withValue($record23[1] . $record23[3] . $record23[4], $dns);
            $eachEvent2 = $eachEvent2->withValue($record23[0], $track);
        }


        $tracks = [];
        $eventTracksArray = [];
        $rader = [];
        foreach ($eachEvent2 as $key => $value) {
            $banaarr = [];
            $track = new TracksForEvents();
            $track->event = $this->createEvent($value);
            foreach ($value as $key2 => $value2) {
                $rader = [];
                $bana = new Bana();
                $bana->bana = $key2;
                if ($eachEvent2->hasValue($key)) {
                    $bana->bana = $key2;
                    foreach ($value2 as $key2 => $rade) {
                            array_push($rader, $rade);
                    }
                   $bana->controls = $rader;
                    array_push($banaarr, $bana);
                }
            }


            $track->tracks = $banaarr;
             array_push( $eventTracksArray,$track);

        }

        foreach ($eventTracksArray as $key2 => $traclforevent) {

            foreach ($traclforevent->tracks as $key2 => $test) {
                     $sites =   $this->createSite($test->controls);
                   $controlls = $this->createControl($test,$sites );
                   $track->track = $this->buildTrackFromCsv($traclforevent, $test, $controlls);

            }
        }
    }


    private function createSite($record): ?array
    {

        $siteDict = new Dictionary();
        $sitereturn = [];
        foreach ($record as $key => $value) {
            if (!$siteDict->hasValue($value[5] . $value[6])) {

                $siteDict = $siteDict->withValue($value[5] . $value[6], new Site("", $value[5], $value[6], $value[13], null,
                    empty($value[8]) ? new DecimalNumber("0") : new DecimalNumber(strval($value[8])),
                    empty($value[9]) ? new DecimalNumber("0") : new DecimalNumber(strval($value[9])),
                    "IMG.JPG"));

                $existingSite = $this->siteRepository->existsByPlaceAndAdress($value[5], $value[6]);

                if ($existingSite == null) {
                    array_push($sitereturn, $this->siteRepository->createSite($siteDict->getValue($value[5] . $value[6])));

                } else {
                    array_push($sitereturn, $existingSite);
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
            if ($events == "") {
                $event->setTitle($value[0][0]);
                $event->setActive(false);
                $event->setCanceled(false);
                $event->setCompleted(false);
                $event->setStartdate($value[0][3]);
                $event->setEnddate($value[0][4]);
                $existsing = $this->eventRepository->existsByTitleAndStartDate($value[0][0], $value[0][3]);
                if ($existsing == null) {
                    $event = $this->eventRepository->createEvent($event);
                } else {
                    $event = $existsing;
                }
            }
        }
        return $event;

    }

    private function createControl($rad, array $track): array
    {

        $checkpoints = [];
        foreach ($rad->controls as $key => $value) {

                $checkpoint = new Checkpoint();
                $sites = $track;
                foreach ($sites as $rowIndex => $record) {
                    $place = $value[5];
                    $adress = $value[6];
                    if ($place == $record->getPlace() && $adress == $record->getAdress()) {
                        $checkpoint->setSiteUid($record->getSiteUid());
                        $checkpoint->setOpens($value[11] != null ? $value[11] : null);
                        $checkpoint->setClosing($value[12] != null ? $value[12] : null);
                        $checkpoint->setDistance(floatval($value[10]));

                        $sss = $this->checkpointRepository->existsBySiteUidAndDistance($checkpoint->getSiteUid(), $checkpoint->getDistance(), $checkpoint->getOpens(), $checkpoint->getClosing());
                        if ($sss == null) {
                             $this->checkpointRepository->createCheckpoint(null, $checkpoint);
                             array_push($checkpoints, $checkpoint);
                       } else {
                                 array_push($checkpoints, $sss);
                      }
                    }
                }
        }

      return $checkpoints;
    }

    private function buildTrackFromCsv(TracksForEvents $trackin,  $rad, array $controlls ): ?Track
    {

        $trackout = null;

        foreach ($rad->controls as $key => $value) {
            $trackwithstartdate = $this->trackRepository->trackWithStartdateExists($trackin->event->getEventUid(), $value[1], $value[11]);
            if($trackwithstartdate){
               return $trackwithstartdate;
            }
            $checkpoints_uid = [];
            $trackToCreate = new Track();
            $trackToCreate->setDescription("");
            $trackToCreate->setTitle($value[1]);
            $trackToCreate->setLink($value[14]);
            $trackToCreate->setHeightdifference(2000);
           $trackToCreate->setDistance($value[2]);
            $trackToCreate->setStartDateTime($value[11]);
            $existintevent =  $this->eventRepository->eventFor($trackin->event->getEventUid());
            if(isset($existintevent)){
                $trackToCreate->setEventUid($trackin->event->getEventUid());
            }

                    if (!empty($controlls)) {
                            foreach ($controlls as $chp => $checkpoint) {
                                array_push($checkpoints_uid, $checkpoint->getCheckpointUid());
                            }
                            $trackToCreate->setCheckpoints($checkpoints_uid);
                    }

            $trackwithstartdate = $this->trackRepository->trackWithStartdateExists($trackToCreate->getEventUid(), $trackToCreate->getTitle(), $value[10]);;
            if ($trackwithstartdate != null) {
                $trackout = $trackwithstartdate;
            }
            $trackout = $this->trackRepository->trackAndCheckpointsExists($trackToCreate->getEventUid(), $trackToCreate->getTitle(), $trackToCreate->getDistance(), $trackToCreate->getCheckpoints());

            if (!$trackout) {
                $trackout = $this->trackRepository->createTrack($trackToCreate);
            }

            break;
        }

            return $trackout;

    }

//    private function createTracksOnEvent($track, $event)
//    {
//        $track = '1e02e4f5-55e0-4211-9b17-23b28836463a';
//        $event = '36c89577-d041-49f8-82ba-b8eb1c6662ba';
//        $tracksOnEvent = $this->eventRepository->trackAndEventOnEvent($event, $track);
//
//
//        if(empty($tracksOnEvent)){
//            $this->eventRepository->createTrackEvent($event, $track);
//        }
//    }




}
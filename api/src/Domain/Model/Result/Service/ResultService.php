<?php

namespace App\Domain\Model\Result\Service;

use App\common\Exceptions\BrevetException;
use App\common\Service\ServiceAbstract;
use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Event\Service\EventService;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Partisipant\Service\ParticipantService;
use App\Domain\Model\Result\Repository\ResultRepository;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Model\Track\Service\TrackService;
use Psr\Container\ContainerInterface;

class ResultService
{

    public function __construct (ContainerInterface $c,
                                 ResultRepository $resultrepository,
                                 TrackRepository $trackRepository,
                                 EventRepository $eventRepository,
                                 ParticipantRepository $participantRepoitory, CompetitorRepository $competitorRepository)
    {
        $this->settings = $c->get('settings');
        $this->resultrepo = $resultrepository;
        $this->trackrepository = $trackRepository;
        $this->eventrepoitory = $eventRepository;
        $this->participantRepository = $participantRepoitory;
        $this->competitorRepository = $competitorRepository;
    }

    public function resultsOnEvent(?string $event_uid, string $year): ?array {

        $track = $this->trackrepository->tracksbyEvent($event_uid);
        $showtrackinfo = false;

        if(count($track) > 1){
            $showtrackinfo = true;
        }

         $result =   $this->resultrepo->getResultsForEvent($event_uid, $year, $showtrackinfo);

        if(empty($result)){
            return array();
        }
        return $result;
    }

    public function trackContestants(?string $event_uid, array $tracks): ?array {
        if(empty($tracks)){
            return array();
        }

        $result =   $this->resultrepo->trackParticipantsOnTrack($event_uid, $tracks);
        return $result;
    }

    public function trackRandonneurOnTrack(?string $competitorUid, ?string $trackUid)
    {

        $competitor = $this->participantRepository->participantFor($competitorUid);
        if($competitor == null){
            throw new BrevetException("Participant not exists", 5, null);
        }

        if($trackUid != ""){
            $track =  $this->trackrepository->getTrackByUid($trackUid);
            if($track == null){
                throw new BrevetException("Track not exists", 5, null);
            }
        }
        $arr = array();
        array_push($arr, $track);
//        print_r($this->resultrepo->trackParticipantOnTrack($competitorUid, $trackUid));
        return $this->resultrepo->trackParticipant($competitorUid, $trackUid);

    }

    public function resultForContestant(string $competitor_uid, string $track_uid, $event_uid): ?array {

        if($competitor_uid != null || $competitor_uid != ""){


            $competitor = $this->competitorRepository->getCompetitorByUID($competitor_uid);

            if($competitor == null){
                throw new BrevetException("Participant not exists", 5, null);
            }

            if($track_uid != ""){
                $track =  $this->trackrepository->getTrackByUid($track_uid);
                if($track == null){
                    throw new BrevetException("Track not exists", 5, null);
                }
            }

           if($event_uid != ""){
               $event = $this->eventrepoitory->eventFor($event_uid);
               if($event == null){
                   throw new BrevetException("Event not exists", 5, null);
               }
           }

            $result = $this->resultrepo->resultForContestant($competitor_uid, $track_uid, $event_uid);

            if(empty($result)){
                return array();
            }


            return $result;
        }

        return array();
    }




}
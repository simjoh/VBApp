<?php

namespace App\Domain\Model\Result\Service;

use App\common\Exceptions\BrevetException;
use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Result\Repository\ResultRepository;
use App\Domain\Model\Track\Repository\TrackRepository;
use Psr\Container\ContainerInterface;

class ResultService
{

    private $settings;
    private $resultrepo;
    private $trackrepository;
    private $eventrepoitory;
    private $participantRepository;
    private $competitorRepository;

    public function __construct(ContainerInterface    $c,
                                ResultRepository      $resultrepository,
                                TrackRepository       $trackRepository,
                                EventRepository       $eventRepository,
                                ParticipantRepository $participantRepoitory, CompetitorRepository $competitorRepository)
    {
        $this->settings = $c->get('settings');
        $this->resultrepo = $resultrepository;
        $this->trackrepository = $trackRepository;
        $this->eventrepoitory = $eventRepository;
        $this->participantRepository = $participantRepoitory;
        $this->competitorRepository = $competitorRepository;
    }

    public function resultsOnEvent(?string $event_uid, string $year): ?array
    {

        $track = $this->trackrepository->tracksbyEvent($event_uid);
        $showtrackinfo = false;

        if (count($track) > 1) {
            $showtrackinfo = true;
        }

        $result = $this->resultrepo->getResultsForEvent($event_uid, $year, $showtrackinfo);

        if (empty($result)) {
            return array();
        }
        return $result;
    }

    public function resultsOnEventNew(?string $event_uid): ?array
    {

        $track = $this->trackrepository->tracksbyEvent($event_uid);
        $result = $this->resultrepo->resultOnEvent($track);

        if (empty($result)) {
            return array();
        }

        $preparedarray = array();

        foreach ($result as $result) {
            $curtrack = $this->trackrepository->getTrackByUid($result['track_uid']);
            $result['trackurl'] = $this->settings['path'] . 'results/track/' . $curtrack->getTrackUid();
            $result['brevetcard'] = $this->settings['path'] . 'track/' . $curtrack->getTrackUid() . '/participant/' . $result['participant_uid'] . '/view';
            array_push($preparedarray, $result);

        }
        return $preparedarray;
    }

    public function resultsOnTrack(?string $track_uid): ?array
    {
        $track = $this->trackrepository->getTrackByUid($track_uid);


        $result = $this->resultrepo->resultOnTrack($track->getTrackUid());
        if (empty($result)) {
            return array();
        }
        $preparedarray = array();
        foreach ($result as $result) {
            $curtrack = $this->trackrepository->getTrackByUid($result['track_uid']);

            $result['brevetcard'] = $this->settings['path'] . 'track/' . $curtrack->getTrackUid() . '/participant/' . $result['participant_uid'] . '/view';
            array_push($preparedarray, $result);

        }
        return $preparedarray;
    }

    public function allresultsForCompetitor(?string $competitor_uid): ?array
    {
        $track = $this->competitorRepository->getCompetitorByUID($competitor_uid);

//        $result = $this->resultrepo->resultOnTrack($track);
//        if (empty($result)) {
//            return array();
//        }
//        return $result;

        return null;
    }


    public function trackContestants(?string $event_uid, array $tracks): ?array
    {
        if (empty($tracks)) {
            return array();
        }

        $result = $this->resultrepo->trackParticipantsOnTrack($event_uid, $tracks);
        return $result;
    }

    public function trackRandonneurOnTrack(?string $competitorUid, ?string $trackUid)
    {

        $competitor = $this->participantRepository->participantFor($competitorUid);
        if ($competitor == null) {
            throw new BrevetException("Participant not exists", 5, null);
        }

        if ($trackUid != "") {
            $track = $this->trackrepository->getTrackByUid($trackUid);
            if ($track == null) {
                throw new BrevetException("Track not exists", 5, null);
            }
        }
        $arr = array();
        array_push($arr, $track);
//        print_r($this->resultrepo->trackParticipantOnTrack($competitorUid, $trackUid));
        return $this->resultrepo->trackParticipant($competitorUid, $trackUid);

    }

    public function resultForContestant(string $competitor_uid, string $track_uid, $event_uid): ?array
    {

        if ($competitor_uid != null || $competitor_uid != "") {


            $competitor = $this->competitorRepository->getCompetitorByUID($competitor_uid);

            if ($competitor == null) {
                throw new BrevetException("Participant not exists", 5, null);
            }

            if ($track_uid != "") {
                $track = $this->trackrepository->getTrackByUid($track_uid);
                if ($track == null) {
                    throw new BrevetException("Track not exists", 5, null);
                }
            }

            if ($event_uid != "") {
                $event = $this->eventrepoitory->eventFor($event_uid);
                if ($event == null) {
                    throw new BrevetException("Event not exists", 5, null);
                }
            }

            $result = $this->resultrepo->resultForContestant($competitor_uid, $track_uid, $event_uid);

            if (empty($result)) {
                return array();
            }


            return $result;
        }

        return array();
    }


}
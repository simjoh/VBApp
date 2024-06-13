<?php

namespace App\Domain\Model\Randonneur\Service;

use App\common\Exceptions\BrevetException;
use App\common\Util;
use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\Event\Repository\EventRepository;
use App\Domain\Model\Partisipant\Participant;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Randonneur\Rest\RandonneurCheckpointAssembly;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\Domain\Model\Track\Rest\TrackAssembly;
use App\Domain\Model\Track\Track;
use Psr\Container\ContainerInterface;

class RandonneurService
{


    public function __construct(CompetitorRepository         $repository,
                                ParticipantRepository        $participantRepository,
                                CheckpointsService           $checkpointsService,
                                TrackRepository              $trackRepository,
                                RandonneurCheckpointAssembly $randonneurCheckpointAssembly,
                                TrackAssembly                $trackAssembly, ContainerInterface $c, EventRepository $eventRepository)
    {
        $this->repository = $repository;
        $this->participantRepository = $participantRepository;
        $this->checkpointService = $checkpointsService;
        $this->trackrepository = $trackRepository;
        $this->randonneurCheckpointAssembly = $randonneurCheckpointAssembly;
        $this->trackAssembly = $trackAssembly;
        $this->eventrepository = $eventRepository;
        $this->settings = $c->get('settings');
    }

    public function checkpointsForRandonneur(?string $track_uid, $startnumber, string $current_user_uid): ?array
    {
        $checkpoints = [];
        $participant = $this->participantRepository->participantOntRackAndStartNumber($track_uid, $startnumber);
        $track = $this->trackrepository->getTrackByUid($track_uid);

        $event = $this->eventrepository->eventFor($track->getEventUid());

        $racepassed = $this->trackrepository->isRacePassed($track_uid);


        if ($event->isCompleted() == true || $racepassed == true) {
            $racepassed = false;
        }

        if ($this->settings['demo'] == 'true') {
            $racepassed = false;
        }


        // kolla om datumet för banan som körs är passerat eller inte

        if (!empty($participant)) {
            // hämta checkpoints for track
            $checkpoints = $this->checkpointService->checkpointForTrack($participant->getTrackUid());
            // return $checkpoints;

            $randonneurcheckpoints = [];
            foreach ($checkpoints as $checkpoint) {
                $stamptime = "";
                $stamped = $this->participantRepository->hasStampOnCheckpoint($participant->getParticipantUid(), $checkpoint->getCheckPointUId());
                $hasDnf = $this->participantRepository->hasDnf($participant->getParticipantUid());
                $hasCheckout = $this->participantRepository->hasCheckedOut($participant->getParticipantUid(),  $checkpoint->getCheckPointUId());

                $participant_checkpoint = $this->participantRepository->stampTimeOnCheckpoint($participant->getParticipantUid(), $checkpoint->getCheckPointUId());
                if ($participant_checkpoint != null) {
                    if ($participant_checkpoint->getPassededDateTime() != null) {
                        $stamptime = $participant_checkpoint->getPassededDateTime();
                    }
                }

                array_push($randonneurcheckpoints, $this->randonneurCheckpointAssembly->toRepresentation($checkpoint, $stamped, $track_uid, $current_user_uid, $startnumber, $hasDnf, false, $stamptime, $hasCheckout));
            }

            return $randonneurcheckpoints;
        }

        return null;
    }

    public function previewCheckpointsForRandonneur(?string $track_uid, string $current_user_uid): ?array
    {

        $track = $this->trackrepository->getTrackByUid($track_uid);

        $checkpoints = $this->checkpointService->checkpointForTrack($track_uid);

        $randonneurcheckpoints = [];

        foreach ($checkpoints as $checkpoint) {
            $stamptime = "";
            $stamped = false;
            $hasDnf = false;
            array_push($randonneurcheckpoints, $this->randonneurCheckpointAssembly->toRepresentationPreview($checkpoint, $stamped, $hasDnf));
        }
        return $randonneurcheckpoints;
    }

    public function checkoutFromCheckpoint(?string $track_uid, $checkpoint_uid, string $startnumber)
    {

        $track = $this->trackrepository->getTrackByUid($track_uid);
        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }

        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);
        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }

        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(), $startnumber);

        if (!isset($participant)) {
            throw new BrevetException("Cannot find participant", 5, null);
        }

        $isStart = $this->checkpointService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        if ($participant->isDns()) {
            throw new BrevetException("You have not started in a race ", 7, null);
        }
        return $this->participantRepository->checkoutFromCheckpoint($participant->getParticipantUid(), $checkpoint_uid, 1, 0);
    }

    public function undoCheckoutFrom(?string $track_uid, ?string $checkpoint_uid, ?string $startnumber, $getAttribute): bool
    {
        $track = $this->trackrepository->getTrackByUid($track_uid);
        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }
        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);
        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }
        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(), $startnumber);
        return $this->participantRepository->undoCheckout($participant->getParticipantUid(), $checkpoint_uid);
    }

    public function stampOnCheckpoint(?string $track_uid, $checkpoint_uid, string $startnumber, string $current_useruid, $lat, $long): bool
    {

        $track = $this->trackrepository->getTrackByUid($track_uid);
        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }

        $today = date('Y-m-d');
        $startdate = date('Y-m-d', strtotime($track->getStartDateTime()));
        // Man ska bara kunna göra incheckning om det är samma da eller senare
//        if ($this->settings['demo'] == 'false') {
//            if ($today < $startdate) {
//                throw new BrevetException("You cannot check in before startdate :  " . $startdate, 6, null);
//            }
//        }

        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);
        //kolla om mindre än 100 meter från kontroll
        $distance = $this->calculateDistancebetweenCordinates($lat, $long, $checkpoint->getSite()->getLat(), $checkpoint->getSite()->getLng(), 'K');
        if ($distance > 0.900) {
            throw new BrevetException("You are not within range of the checkpoint", 7, null);
        }

        if ($this->settings['demo'] == 'false') {
            if ($today < $startdate) {
                throw new BrevetException("You cannot check in before startdate :  " . $startdate, 6, null);
            }
        }

        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }

        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(), $startnumber);

        if (!isset($participant)) {
            throw new BrevetException("Cannot find participant", 5, null);
        }

        $isStart = $this->checkpointService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        if ($participant->isDns()) {
            throw new BrevetException("You have not started in a race ", 7, null);
        }
        if ($this->settings['demo'] == 'false') {
            // är det start behöver vi inte göra kontroller då sätts starttiden till loppets starttid
            if ($isStart == false) {
                // kolla att kontrollern har öppnat
                if (date('Y-m-d H:i:s') < $checkpoint->getOpens()) {
                    //     throw new BrevetException("Checkpoint not open. Opening date time:  " . date("Y-m-d H:i:s", strtotime($checkpoint->getOpens())), 6, null);
                }
            }
            // kolla att kontrollen har stängt
            if (date('Y-m-d H:i:s') > $checkpoint->getClosing()) {
                //  throw new BrevetException("Checkpoint is closed. Closing date time: " . date("Y-m-d H:i:s", strtotime($checkpoint->getClosing())), 6, null);
            }

        }

        // kolla om start eller mål

        if ($isStart == true) {
            if ($this->settings['demo'] == 'false') {
                if ($today < $startdate) {
                    throw new BrevetException("You cannot check in before startdate :  " . $startdate, 7, null);
                }
            }

            if (date('Y-m-d H:i:s') < $track->getStartDateTime()) {
                $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1, 0, $lat, $long);
                $this->participantRepository->checkoutFromCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, 1, 0, $track->getStartDateTime());
            } else if (date('Y-m-d H:i:s') < $checkpoint->getClosing() && date('Y-m-d H:i:s') > $track->getStartDateTime()) {
                $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1, 0, $lat, $long);
                // kan vara intressant att veta när man verkligen startade
                $this->participantRepository->checkoutFromCheckpoint($participant->getParticipantUid(), $checkpoint_uid, 1, 0);
            } else if (date('Y-m-d H:i:s') > $checkpoint->getClosing()) {
                if ($this->settings['demo'] == 'false') {
                    if (date('Y-m-d H:i:s') > $checkpoint->getClosing()) {
                        //   throw new BrevetException("Checkpoint is closed. Closing date time: " . date("Y-m-d H:i:s", strtotime($checkpoint->getClosing())), 6, null);
                    }
                    $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1, 0, $lat, $long);
                    $this->participantRepository->checkoutFromCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, 1, 0, $track->getStartDateTime());
                } else {
                    $this->participantRepository->stampOnCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, $track->getStartDateTime(), 1, 0, $lat, $long);
                    $this->participantRepository->checkoutFromCheckpointWithTime($participant->getParticipantUid(), $checkpoint_uid, 1, 0, $track->getStartDateTime());
                }
            } else {
                throw new BrevetException("Error on check in", 1, null);
            }
            $participant->setStarted(1);
            $this->participantRepository->updateParticipant($participant);
            return true;
        }

        $isEnd = $this->checkpointService->isEndCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());
        if ($isEnd == true) {

            if ($participant->isDnf() == true) {
                throw new BrevetException("You cannot finsish race if dnf is set", 7, null);
            }

            $countCheckpoints = $this->checkpointService->countCheckpointsForTrack($participant->getTrackUid());
            $oktofinish = $this->participantRepository->participantHasStampOnAllExceptFinish($track_uid, $checkpoint->getCheckpointUid(), $participant->getParticipantUid(), $countCheckpoints);

//            if($oktofinish == false) {
//                throw new BrevetException("Cannot checkin on finish checkpoint due to missed checkins on one or more checkpoints. Contact race administrator", 6, null);
//            }

            if ($this->settings['demo'] == 'false') {
                if ($track->getStartDateTime() != '-') {
                    // om mål sätt måltid till tiden för instämpling och beräkna tiden mella första och sista instämpling. Sätt totaltiden i participant och markera finished
                    if (date('Y-m-d H:i:s') < $track->getStartDateTime()) {
                        throw new BrevetException("Can not finish before the start of the race " . date("Y-m-d H:i:s", strtotime($track->getStartDateTime())), 1, null);
                    }
                }
            }

            $this->participantRepository->stampOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid, 1, 0, $lat, $long);
            $this->participantRepository->checkoutFromCheckpoint($participant->getParticipantUid(), $checkpoint_uid, 1, 0);
            $participant->setDnf(false);
            $participant->setDns(false);

            $participant->setFinished(true);
            // beräkna tiden från första incheckning till nu och sätt tiden
            $participant->setTime(Util::calculateSecondsBetween($track->getStartDateTime()));
            $this->participantRepository->updateParticipant($participant);
            return true;
        }

        if ($participant->isStarted() == false) {
            throw new BrevetException("You have to check in on startcheckpoint before this", 7, null);
        }

        $this->participantRepository->stampOnCheckpoint($participant->getParticipantUid(), $checkpoint_uid, 1, 0, $lat, $long);
        return true;

    }

    public function markAsDnf(?string $track_uid, $checkpoint_uid, string $startnumber, string $current_useruid): bool
    {

        $track = $this->trackrepository->getTrackByUid($track_uid);

        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }
        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);


        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }

        $today = date('Y-m-d');
        $startdate = date('Y-m-d', strtotime($track->getStartDateTime()));

        if ($this->settings['demo'] == 'false') {
            if ($today < $startdate) {
                throw new BrevetException("Check in opens on startdate:  " . $startdate, 7, null);
            }
        }


        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(), $startnumber);

        if (!isset($participant)) {
            throw new BrevetException("Cannot find participant", 5, null);
        }

        if ($participant->isStarted() == false) {
            throw new BrevetException("You must start before you can abandon race", 6, null);
        }

        return $this->participantRepository->setDnf($participant->getParticipantUid());

    }


    public function rollbackDnf(?string $track_uid, $checkpoint_uid, string $startnumber, string $current_useruid): bool
    {

        $track = $this->trackrepository->getTrackByUid($track_uid);

        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }
        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);

        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }


        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(), $startnumber);

        if (!isset($participant)) {
            throw new BrevetException("Cannot find participant", 5, null);
        }

        return $this->participantRepository->rollbackDnf($participant->getParticipantUid());

    }

    public function markAsDns(?string $track_uid, $checkpoint_uid, string $startnumber, string $current_useruid)
    {
        $track = $this->trackrepository->getTrackByUid($track_uid);

        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }
        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);


        $today = date('Y-m-d');
        $startdate = date('Y-m-d', strtotime($track->getStartDateTime()));

        if ($this->settings['demo'] == 'false') {
            if ($today < $startdate) {
                throw new BrevetException("You cannot set DNS before startdate :  " . $startdate, 7, null);
            }
        }

        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }


        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(), $startnumber);

        if (!isset($participant)) {
            throw new BrevetException("Cannot find participant", 5, null);
        }

        $this->participantRepository->setDns($participant->getParticipantUid());

    }

    public function rollbackStamp(?string $track_uid, $checkpoint_uid, string $startnumber, string $current_useruid): bool
    {
        $track = $this->trackrepository->getTrackByUid($track_uid);

        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }
        $checkpoint = $this->checkpointService->checkpointFor($checkpoint_uid);

        if (!isset($checkpoint)) {
            throw new BrevetException("Checkpoint not exists", 5, null);
        }

        $participant = $this->participantRepository->participantOntRackAndStartNumber($track->getTrackUid(), $startnumber);

        if (!isset($participant)) {
            throw new BrevetException("Cannot find participant", 5, null);
        }

        $isEnd = $this->checkpointService->isEndCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());

        if ($isEnd == true) {
            $this->participantRepository->rollbackStamp($participant->getParticipantUid(), $checkpoint_uid);
//            $participant->setDnf(false);
//            $participant->setDns(false);
            $participant->setFinished(false);
            $participant->setTime(null);
            $this->participantRepository->updateParticipant($participant);
            $this->participantRepository->undoCheckout($participant->getParticipantUid(), $checkpoint_uid);
            return true;
        }

        $isStart = $this->checkpointService->isStartCheckpoint($participant->getTrackUid(), $checkpoint->getCheckpointUid());
        if ($isStart == true) {
            $participant->setStarted(false);
            $participant->setTime(null);
            $this->participantRepository->updateParticipant($participant);
        }

        return $this->participantRepository->rollbackStamp($participant->getParticipantUid(), $checkpoint_uid);
    }

    public function getChecpointsForRandonneurForAdmin(Participant $participant, Track $track)
    {
        $checkpoints = [];
        $event = $this->eventrepository->eventFor($track->getEventUid());

        $racepassed = $this->trackrepository->isRacePassed($track->getTrackUid());


//        if($event->isCompleted() == true || $racepassed == true) {
//            $racepassed = true;
//        }

        if ($this->settings['demo'] == 'true') {
            $racepassed = false;
        }

        // kolla om datumet för banan som körs är passerat eller inte

        if (!empty($participant)) {
            // hämta checkpoints for track
            $checkpoints = $this->checkpointService->checkpointForTrack($participant->getTrackUid());
            // return $checkpoints;
            $randonneurcheckpoints = [];
            foreach ($checkpoints as $checkpoint) {
                $stamptime = "";
                $stamped = $this->participantRepository->hasStampOnCheckpoint($participant->getParticipantUid(), $checkpoint->getCheckPointUId());
                $hasDnf = $this->participantRepository->hasDnf($participant->getParticipantUid());
                $participant_checkpoint = $this->participantRepository->stampTimeOnCheckpoint($participant->getParticipantUid(), $checkpoint->getCheckPointUId());

                if ($participant_checkpoint != null) {
                    if ($participant_checkpoint->getPassededDateTime() != null) {
                        $stamptime = $participant_checkpoint->getPassededDateTime();
                    }
                }
                array_push($randonneurcheckpoints, $this->randonneurCheckpointAssembly->toRepresentationForAdmin($participant->getParticipantUid(), $checkpoint, $stamped, $track->getTrackUid(), $hasDnf, $racepassed, $stamptime));
            }

            return $randonneurcheckpoints;
        }

        return null;

    }

    function calculateDistancebetweenCordinates($lat1, $lon1, $lat2, $lon2, $unit)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = strtoupper($unit);

        if ($unit == "K") {
            return ($miles * 1.609344);
        } else if ($unit == "N") {
            return ($miles * 0.8684);
        } else {
            return $miles;

        }
    }


}
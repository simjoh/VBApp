<?php

namespace App\Domain\Model\Result\Service;

use App\Domain\Model\Result\Repository\ResultRepository;
use Psr\Container\ContainerInterface;

class ResultService
{

    public function __construct (ContainerInterface $c, ResultRepository $resultrepository)
    {
        $this->settings = $c->get('settings');
        $this->resultrepo = $resultrepository;
    }

    public function resultsOnEvent(?string $event_uid, string $year): ?array {
         $result =   $this->resultrepo->getResultsForEvent($event_uid, $year);

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

    public function resultForContestant(string $competitor_uid, string $track_uid, $event_uid): ?array {

        if($competitor_uid != null || $competitor_uid != ""){

            $result = $this->resultrepo->resultForContestant($competitor_uid, $track_uid, $event_uid);

            if(empty($result)){
                return array();
            }


            return $result;
        }

        return array();
    }

}
<?php

namespace App\Domain\Model\Partisipant\Service;

use App\Domain\Model\Partisipant\Repository\ParticipantRepository;
use App\Domain\Model\Track\Repository\TrackRepository;
use Psr\Container\ContainerInterface;

class ParticipantService
{

    public function __construct(ContainerInterface $c ,
                                TrackRepository $trackRepository, ParticipantRepository $participantRepository)
    {
        $this->trackRepository = $trackRepository;
        $this->participantRepository = $participantRepository;
    }

    public function participantsOnTrack(string $trackuid): array {
        $participants = $this->participantRepository->participantsOnTrack($trackuid);
        if(!isset($events)){
            return $participants;
        }

        return array();
    }

}
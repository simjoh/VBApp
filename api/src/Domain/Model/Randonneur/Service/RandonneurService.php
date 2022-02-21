<?php

namespace App\Domain\Model\Randonneur\Service;

use App\Domain\Model\CheckPoint\Service\CheckpointsService;
use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\Partisipant\Repository\ParticipantRepository;

class RandonneurService
{


    public function __construct(CompetitorRepository $repository, ParticipantRepository $participantRepository, CheckpointsService $checkpointsService)
    {
        $this->repository = $repository;
         $this->participantRepository = $participantRepository;
        $this->checkpointService = $checkpointsService;
    }

    public function checkpointsForRandonneur(?string $track_uid, $startnumber): ?array {

        $participant = $this->participantRepository->participantOntRackAndStartNumber($track_uid, $startnumber);

        if(!empty($participant)){
            // hämta checkpoints for track

            return $this->checkpointService->checkpointForTrack($participant->getTrackUid());
        }

        return null;
    }
}
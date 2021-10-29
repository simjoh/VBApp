<?php

namespace App\Domain\Model\CheckPoint\Service;


use App\Domain\Model\CheckPoint\Checkpoint;
use App\Domain\Model\CheckPoint\Repository\CheckpointRepository;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
use App\Domain\Model\Site\Service\SiteService;
use Psr\Container\ContainerInterface;

class CheckpointsService
{
     private $checkpointRepository;
     private $siteservice;
    public function __construct(ContainerInterface $c, CheckpointRepository $checkpointRepository, SiteService $siteService)
    {
        $this->checkpointRepository = $checkpointRepository;
        $this->siteservice = $siteService;
    }


    public function checkpointsFor(array $checkpoints_uid) : array{

        $checkpoints = $this->checkpointRepository->checkpointsFor($checkpoints_uid);
        return $this->toRepresentations($checkpoints);
    }

    private function toRepresentations($checkpointsArray): array
    {
        $checkpoints = array();
        foreach ($checkpointsArray as $x =>  $checkpoint) {
            array_push($checkpoints, (object) $this->toRepresentation($checkpoint));
        }
        return $checkpoints;
    }

    private function toRepresentation(Checkpoint $checkpoint): CheckpointRepresentation
    {

        $checkpointRepresentation =  new CheckpointRepresentation();
        $checkpointRepresentation->setCheckpointUid($checkpoint->getCheckpointUid());
        $checkpointRepresentation->setDescription($checkpoint->getDescription());
        $checkpointRepresentation->setTitle($checkpoint->getTitle());
      $checkpointRepresentation->setSite($this->siteservice->siteFor($checkpoint->getSiteUid()));
        return $checkpointRepresentation;
    }

}
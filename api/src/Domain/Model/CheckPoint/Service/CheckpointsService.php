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

    public function allCheckpoints() : array{
        $checkpoints = $this->checkpointRepository->allCheckpoints();
        return $this->toRepresentations($checkpoints);
    }

    public function checkpointsFor(array $checkpoints_uid) : array{
        $checkpoints = $this->checkpointRepository->checkpointsFor($checkpoints_uid);
        return $this->toRepresentations($checkpoints);
    }

    public function checkpointFor(?string $checkpoint_uid) : CheckpointRepresentation
    {

        return $this->toRepresentation($this->checkpointRepository->checkpointFor($checkpoint_uid));
    }

    public function updateCheckpoint(?string $checkpoint_uid, CheckpointRepresentation $checkpointRepresentation): CheckpointRepresentation
    {
        $checkpoint = $this->toCheckpoint($checkpointRepresentation);
        $checkpointtoreturn =  $this->checkpointRepository->updateCheckpoint($checkpoint_uid, $checkpoint);
        return  $this->toRepresentation($checkpointtoreturn);

    }

    public function createCheckpoint(?string $checkpoint_uid, CheckpointRepresentation $checkpoint)
    {
        $checkpoint =  $this->checkpointRepository->createCheckpoint($checkpoint_uid, $this->toCheckpoint($checkpoint));
        return $this->toRepresentation($checkpoint);
    }

    public function deleteCheckpoint(?string $checkpoint_uid)
    {
        $this->checkpointRepository->deleteCheckpoint($checkpoint_uid);
    }


    //To och from representation
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

    private function toCheckpoint(CheckpointRepresentation $checkpointRepresentation): Checkpoint {


        $checkpoint =new Checkpoint();
        if(!empty($checkpointRepresentation->getSite())){
            $checkpoint->setSiteUid($checkpointRepresentation->getSite()->getSiteUid());
        }

        $checkpoint->setDistance($checkpointRepresentation->getDistance() == null ? 0 : $checkpointRepresentation->getDistance());
        $checkpoint->setTitle($checkpointRepresentation->getTitle() == null ? "" : $checkpointRepresentation->getTitle());
        $checkpoint->setDescription($checkpointRepresentation->getDescription() == null ? "" : $checkpointRepresentation->getDescription());
        $checkpoint->setOpens($checkpointRepresentation->getOpens() == null ? null : $checkpointRepresentation->getOpens());
        $checkpoint->setClosing($checkpointRepresentation->getClosing());
        $checkpoint->setCheckpointUid($checkpointRepresentation->getCheckpointUid());
        return $checkpoint;
    }




}
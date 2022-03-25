<?php

namespace App\Domain\Model\CheckPoint\Service;


use App\common\Service\ServiceAbstract;
use App\Domain\Model\CheckPoint\Checkpoint;
use App\Domain\Model\CheckPoint\Repository\CheckpointRepository;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
use App\Domain\Model\Site\Service\SiteService;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;

class CheckpointsService extends ServiceAbstract
{
     private $checkpointRepository;
     private $siteservice;
    public function __construct(ContainerInterface $c, CheckpointRepository $checkpointRepository, SiteService $siteService, PermissionRepository $permissionRepository )
    {
        $this->checkpointRepository = $checkpointRepository;
        $this->siteservice = $siteService;
        $this->permissionrepository = $permissionRepository;
    }

    public function allCheckpoints() : array{
        $checkpoints = $this->checkpointRepository->allCheckpoints();
        return $this->toRepresentations($checkpoints);
    }

    public function checkpointsFor(array $checkpoints_uid, string $currentUserUid) : array{
        $permissions = $this->getPermissions($currentUserUid);
        $checkpoints = $this->checkpointRepository->checkpointsFor($checkpoints_uid);
        return $this->toRepresentations($checkpoints);
    }

    public function checkpointFor(?string $checkpoint_uid) : CheckpointRepresentation
    {

        return $this->toRepresentation($this->checkpointRepository->checkpointFor($checkpoint_uid));
    }

    public function checkpointForTrack(?string $track_uid) : array
    {

        $checkpointUIDS = $this->checkpointRepository->checkpointUidsForTrack($track_uid);

        $checkpoints = $this->checkpointRepository->checkpointsFor($checkpointUIDS);

        return $this->toRepresentations($checkpoints);
    }


    public function isStartCheckpoint(?string $track_uid, string $checkpoint_uid) : bool
    {

        $checkpointUIDS = $this->checkpointRepository->checkpointUidsForTrack($track_uid);

        $checkpoints = $this->checkpointRepository->isStartCheckpoin($checkpointUIDS);

        if($checkpoints == $checkpoint_uid){
            return true;
        } else {
            return false;
        }
    }


    public function isEndCheckpoint(?string $track_uid, string $checkpoint_uid) : bool
    {

        $checkpointUIDS = $this->checkpointRepository->checkpointUidsForTrack($track_uid);
        $checkpoints = $this->checkpointRepository->isEndCheckpoin($checkpointUIDS);
        if($checkpoints == $checkpoint_uid){
            return true;
        } else {
            return false;
        }
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
        $checkpointRepresentation->setDistance($checkpoint->getDistance());
        $checkpointRepresentation->setOpens($checkpoint->getOpens());
        $checkpointRepresentation->setClosing($checkpoint->getClosing());
        $checkpointRepresentation->setSite($this->siteservice->siteFor($checkpoint->getSiteUid(),'s'));
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


    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("CHECKPOINT",$user_uid);
    }
}
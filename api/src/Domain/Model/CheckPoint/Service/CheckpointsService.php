<?php

namespace App\Domain\Model\CheckPoint\Service;


use App\common\Service\ServiceAbstract;
use App\Domain\Model\CheckPoint\Checkpoint;
use App\Domain\Model\CheckPoint\Repository\CheckpointRepository;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
use App\Domain\Model\Site\Service\SiteService;
use App\Domain\Permission\PermissionRepository;
use Psr\Container\ContainerInterface;
use App\Domain\Model\Track\Repository\TrackRepository;
use App\common\Brevetcalculator\ACPBrevetCalculator;
use App\common\Exceptions\BrevetException;
use App\Domain\Model\CheckPoint\Rest\CheckpointAssembly;

class CheckpointsService extends ServiceAbstract
{
    private $checkpointRepository;
    private $siteservice;
    private $permissionrepository;
    private $trackRepository;
    private $checkpointAssembly;

    public function __construct(
        ContainerInterface $c,
        CheckpointRepository $checkpointRepository,
        SiteService $siteService,
        PermissionRepository $permissionRepository,
        TrackRepository $trackRepository,
        CheckpointAssembly $checkpointAssembly
    ) {
        $this->checkpointRepository = $checkpointRepository;
        $this->siteservice = $siteService;
        $this->permissionrepository = $permissionRepository;
        $this->trackRepository = $trackRepository;
        $this->checkpointAssembly = $checkpointAssembly;
    }

    public function allCheckpoints(): array
    {
        $checkpoints = $this->checkpointRepository->allCheckpoints();
        return $this->checkpointAssembly->toRepresentations($checkpoints);
    }

    public function checkpointsFor(array $checkpoints_uid, string $currentUserUid): array
    {
        $permissions = $this->getPermissions($currentUserUid);
        $checkpoints = $this->checkpointRepository->checkpointsFor($checkpoints_uid);
        return $this->checkpointAssembly->toRepresentations($checkpoints);
    }

    public function checkpointFor(?string $checkpoint_uid): CheckpointRepresentation
    {
        return $this->checkpointAssembly->toRepresentation($this->checkpointRepository->checkpointFor($checkpoint_uid));
    }

    public function checkpointForTrack(?string $track_uid): array
    {

        $checkpointUIDS = $this->checkpointRepository->checkpointUidsForTrack($track_uid);

        $checkpoints = $this->checkpointRepository->checkpointsFor($checkpointUIDS);

        return $this->checkpointAssembly->toRepresentations($checkpoints);
    }

    public function countCheckpointsForTrack(?string $track_uid): int {

             return $this->checkpointRepository->countCheckpointsForTrack($track_uid);
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
        $checkpoint = $this->checkpointAssembly->toCheckpoint($checkpointRepresentation);
        $checkpointtoreturn =  $this->checkpointRepository->updateCheckpoint($checkpoint_uid, $checkpoint);
        return  $this->checkpointAssembly->toRepresentation($checkpointtoreturn);

    }

    public function createCheckpoint(?string $checkpoint_uid, CheckpointRepresentation $checkpoint)
    {
        $checkpoint = $this->checkpointRepository->createCheckpoint($checkpoint_uid, $this->checkpointAssembly->toCheckpoint($checkpoint));
        return $this->checkpointAssembly->toRepresentation($checkpoint);
    }

    public function deleteCheckpoint(?string $checkpoint_uid)
    {
        $this->checkpointRepository->deleteCheckpoint($checkpoint_uid);
    }

    public function addCheckpointsToTrack(string $track_uid, array $checkpoints): array
    {
        // Get track information
        $track = $this->trackRepository->getTrackByUid($track_uid);
        if (!isset($track)) {
            throw new BrevetException("Track not exists", 5, null);
        }

        // Initialize ACP calculator with track distance and start time
        $calculator = new ACPBrevetCalculator(
            floatval($track->getDistance()),
            $track->getStartDateTime()
        );

        // Create checkpoints with calculated times
        $createdCheckpoints = [];
        foreach ($checkpoints as $index => $checkpoint) {
            // Validate required distance
            if (!isset($checkpoint->distance)) {
                throw new BrevetException("Distance is required for checkpoint " . ($index + 1), 1, null);
            }
            
            $distance = floatval($checkpoint->distance);
            if ($distance < 0) {
                throw new BrevetException("Distance cannot be negative for checkpoint " . ($index + 1), 1, null);
            }
            
            // Calculate opening and closing times for this checkpoint
            $openDateTime = $calculator->getOpeningDateTime($distance);
            $closeDateTime = $calculator->getClosingDateTime($distance);
            
            // Convert checkpoint to representation format
            $checkpointRep = new CheckpointRepresentation();
            $checkpointRep->setDistance($distance);
            $checkpointRep->setTitle($checkpoint->title ?? '');
            $checkpointRep->setDescription($checkpoint->description ?? '');
            // Always set the calculated open/close times from ACP calculator
            $checkpointRep->setOpens($openDateTime->format('Y-m-d H:i:s'));
            $checkpointRep->setClosing($closeDateTime->format('Y-m-d H:i:s'));
            if (isset($checkpoint->site) && isset($checkpoint->site->site_uid)) {
                $siteRepresentation = $this->siteservice->siteFor($checkpoint->site->site_uid, 's');
                if ($siteRepresentation) {
                    $checkpointRep->setSite($siteRepresentation);
                }
            }

            // Create checkpoint with null UID since it's a new checkpoint
            $createdCheckpoint = $this->createCheckpoint(null, $checkpointRep);
            
            // Add checkpoint to track
            $this->checkpointRepository->addCheckpointToTrack($track_uid, $createdCheckpoint->getCheckpointUid());
            
            $createdCheckpoints[] = $createdCheckpoint;
        }

        return $createdCheckpoints;
    }

    public function getPermissions($user_uid): array
    {
        return $this->permissionrepository->getPermissionsTodata("CHECKPOINT",$user_uid);
    }
}
<?php

namespace App\Domain\Model\CheckPoint\Rest;

use App\Domain\Model\CheckPoint\Checkpoint;
use App\Domain\Model\Site\Service\SiteService;
use Psr\Container\ContainerInterface;

class CheckpointAssembly
{
    private $siteservice;
    private $settings;

    public function __construct(ContainerInterface $c, SiteService $siteService)
    {
        $this->siteservice = $siteService;
        $this->settings = $c->get('settings');
    }

    public function toRepresentations(array $checkpointsArray): array
    {
        $checkpoints = array();
        foreach ($checkpointsArray as $x => $checkpoint) {
            array_push($checkpoints, (object) $this->toRepresentation($checkpoint));
        }
        return $checkpoints;
    }

    public function toRepresentationsOnly(array $checkpointsArray): array
    {
        $checkpoints = array();
        foreach ($checkpointsArray as $x => $checkpoint) {
            array_push($checkpoints, (object) $this->toRepresentationOnly($checkpoint));
        }
        return $checkpoints;
    }

    public function toRepresentation(Checkpoint $checkpoint): CheckpointRepresentation
    {
        $checkpointRepresentation = new CheckpointRepresentation();
        $checkpointRepresentation->setCheckpointUid($checkpoint->getCheckpointUid());
        $checkpointRepresentation->setDescription($checkpoint->getDescription());
        $checkpointRepresentation->setTitle($checkpoint->getTitle());

        $checkpointRepresentation->setDistance($checkpoint->getDistance());
        $checkpointRepresentation->setOpens($checkpoint->getOpens());
        if ($checkpoint->getClosing() !== null) {
            $checkpointRepresentation->setClosing($checkpoint->getClosing());
        }

        $checkpointRepresentation->setSite($this->siteservice->siteFor($checkpoint->getSiteUid(), 's'));
        return $checkpointRepresentation;
    }

    public function toRepresentationOnly(Checkpoint $checkpoint): CheckpointRepresentation
    {
        $checkpointRepresentation = new CheckpointRepresentation();
        $checkpointRepresentation->setCheckpointUid($checkpoint->getCheckpointUid());
        $checkpointRepresentation->setDescription($checkpoint->getDescription());
        $checkpointRepresentation->setTitle($checkpoint->getTitle());
        $checkpointRepresentation->setDistance($checkpoint->getDistance());
        $checkpointRepresentation->setOpens($checkpoint->getOpens());
        
        if ($checkpoint->getClosing() !== null) {
            $checkpointRepresentation->setClosing($checkpoint->getClosing());
        }

        // Include site information but no state data
        $checkpointRepresentation->setSite($this->siteservice->siteFor($checkpoint->getSiteUid(), 's'));
        
        return $checkpointRepresentation;
    }

    public function toCheckpoint(CheckpointRepresentation $checkpointRepresentation): Checkpoint
    {
        $checkpoint = new Checkpoint();
        if (!empty($checkpointRepresentation->getSite())) {
            $checkpoint->setSiteUid($checkpointRepresentation->getSite()->getSiteUid());
        }

        $checkpoint->setDistance($checkpointRepresentation->getDistance() ?? 0);
        $checkpoint->setTitle($checkpointRepresentation->getTitle() ?? "");
        $checkpoint->setDescription($checkpointRepresentation->getDescription() ?? "");
        $checkpoint->setOpens($checkpointRepresentation->getOpens());
        $checkpoint->setClosing($checkpointRepresentation->getClosing());
        if ($checkpointRepresentation->getCheckpointUid() !== null) {
            $checkpoint->setCheckpointUid($checkpointRepresentation->getCheckpointUid());
        }
        return $checkpoint;
    }
} 
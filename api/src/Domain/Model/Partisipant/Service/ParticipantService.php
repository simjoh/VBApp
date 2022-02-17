<?php

namespace App\Domain\Model\Partisipant\Service;

use App\Domain\Model\Track\Repository\TrackRepository;
use Psr\Container\ContainerInterface;

class PartisipantService
{

    public function __construct(ContainerInterface $c ,
                                TrackRepository $trackRepository)
    {
        $this->trackRepository = $trackRepository;
    }

}
<?php

namespace App\common\Domain\Competitor\Service;

use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\User\Repository\UserRepository;

class CompetitorService
{

    /**
     * The constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(CompetitorRepository $repository)
    {
        $this->repository = $repository;
    }
}
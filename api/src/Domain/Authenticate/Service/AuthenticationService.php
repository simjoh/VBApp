<?php

namespace App\Domain\Authenticate\Service;


use App\Domain\Model\Competitor\Competitor;
use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\User\User;

class AuthenticationService
{

    public function __construct(UserRepository $repository, CompetitorRepository $competitorRepository)
    {
        $this->repository = $repository;
        $this->competitorrepository = $competitorRepository;
    }

    public function authenticate($username, $password): ?User
    {
        $user = $this->repository->authenticate($username, $password);

        if (isset($user)) {
            return $user;
        }

        return null;
    }

    public function authenticateCompetitor($username, $password): ?Competitor {
        $competitor = $this->competitorrepository->authenticate($username, $password);
        if (isset($competitor)) {
            return $competitor;
        }

        return null;
    }
}
<?php

namespace App\Domain\Authenticate\Service;


use App\Domain\Model\Competitor\Competitor;
use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\User\User;

class AuthenticationService
{

    private $competitorrepository;
    private $repository;


    public function __construct(UserRepository $repository, CompetitorRepository $competitorRepository)
    {
        $this->repository = $repository;
        $this->competitorrepository = $competitorRepository;
    }

    public function authenticate($username, $password): ?User
    {

        $usera = $this->repository->authenticate($username, $password);



        if (isset($usera)) {
            return $usera;
        }
        return null;
    }

    public function authenticateCompetitor($username, $password): ?Competitor {
        $competitor = $this->competitorrepository->authenticate2($username, $password);
        if (isset($competitor)) {
            return $competitor;
        }

        return null;
    }
}
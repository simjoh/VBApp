<?php

namespace App\Domain\Model\User\Service;

use App\Domain\Model\Competitor\Competitor;
use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\User\User;
use App\Domain\Ping\Repository\PingRepository;

class UserService
{

    /**
     * @var UserRepository
     */
    private UserRepository $repository;

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }



    public function getAllUsers(): ?array {
        $allUsers = $this->repository->getAllUSers();
        if (isset($allUsers)) {
            return $allUsers;
        }

        return null;
    }



}
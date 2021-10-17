<?php

namespace App\Domain\Model\User\Service;

use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\User\User;
use App\Domain\Ping\Repository\PingRepository;

class UserService
{

    /**
     * @var PingRepository
     */
    private UserRepository $repository;

    /**
     * The constructor.
     *
     * @param UserRepository $repository
     */
    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }



}
<?php

namespace App\Domain\Authenticate\Service;

use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\User\User;

class AuthenticationService
{

    public function __construct(UserRepository $repository)
    {
        $this->repository = $repository;
    }

    public function authenticate(): ?User
    {

        $pass = 'secret';
        $users = 'admin';
//        $user = $this->repository->authenticate();
//
//        if($user->getUsername() == 'admin'){
//            return null;
//        }

        return new User(1, 'admin','Admin', 'Asminsson', '');

    }
}
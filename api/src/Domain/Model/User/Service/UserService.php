<?php

namespace App\Domain\Model\User\Service;

use App\common\Rest\Link;
use App\Domain\Model\User\Repository\UserRepository;
use App\Domain\Model\User\Rest\UserRepresentation;
use App\Domain\Model\User\User;

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
            return $this->toRepresentations($allUsers);

        }


        return null;
    }

    public function getUserById($id): ?User {
        $allUsers = $this->repository->getUserById($id);
        if (isset($allUsers)) {
            return $allUsers;
        }

        return null;
    }

    public function updateUser($id, User $userParsed): ?User {
        $user = $this->repository->updateUser($id, $userParsed);
        if (isset($user)) {
            return $user;
        }

        return null;
    }

    public function createUser(UserRepresentation $userrepresentation): UserRepresentation {
       $newUser = $this->repository->createUser($this->toSite($userrepresentation));
       return $this->toRepresentation($newUser);
    }

    public function deleteUser($user_uid): void{

        $this->repository->deleteUser($user_uid);
    }


    private function toRepresentations(array $UserArray): array {
        $userArray = array();
        foreach ($UserArray as $x =>  $site) {
            array_push($userArray, (object) $this->toRepresentation($site));
        }
        return $userArray;
    }
    private function toRepresentation(User $s): UserRepresentation {
        $userRepresentation = new UserRepresentation();
        $userRepresentation->setUserUid($s->getId());
        $userRepresentation->setUsername($s->getUsername());
        $userRepresentation->setGivenname($s->getGivenname());
        $userRepresentation->setFamilyname($s->getFamilyname());
        // bygg på med lite länkar
        $link = new Link();
        $userRepresentation->setLink($link);
        return $userRepresentation;
    }

    private function toSite(UserRepresentation $site){
        $user = new User();
        $user->setGivenname($site->getGivenname());
        $user->setFamilyname($site->getFamilyname());
        $user->setUsername($site->getUsername());
        return $user;
    }




}
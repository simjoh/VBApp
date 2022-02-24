<?php

namespace App\Domain\Model\Competitor\Service;

use App\Domain\Model\Competitor\Competitor;
use App\Domain\Model\Competitor\CompetitorInfo;
use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\User\Repository\UserRepository;
use Cassandra\Date;
use DateTime;
use Ramsey\Uuid\Uuid;

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


    public function getCompetitorByNameAndBirthDate(string $givenname, string $familyname, string $date ): ?Competitor {
        if(isset($date)){
           $dateconverterted =  date("Y-m-d", strtotime($date));
        }

        return $this->repository->getCompetitorByNameAndBirthDate($givenname, $familyname, $dateconverterted );
    }

    public function createCompetitor(string $givenName, string $familyName, string $userName, string $birthdate): Competitor {
       return $this->repository->createCompetitor($givenName,$familyName,$userName,$birthdate);
    }
}
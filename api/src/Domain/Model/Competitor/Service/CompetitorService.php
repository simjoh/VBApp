<?php

namespace App\Domain\Model\Competitor\Service;

use App\Domain\Model\Competitor\Competitor;
use App\Domain\Model\Competitor\CompetitorInfo;
use App\Domain\Model\Competitor\Repository\CompetitorRepository;
use App\Domain\Model\Competitor\Rest\CompetitorAssembly;
use App\Domain\Model\Competitor\Rest\CompetitorRepresentation;
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
    public function __construct(CompetitorRepository $repository, CompetitorAssembly $competitorAssembly)
    {
        $this->repository = $repository;
        $this->competitorAssembly = $competitorAssembly;
    }


    public function getCompetitorByNameAndBirthDate(string $givenname, string $familyname, string $date ): ?Competitor {
        if(isset($date)){
           $dateconverterted =  date("Y-m-d", strtotime($date));
        }

        return $this->repository->getCompetitorByNameAndBirthDate($givenname, $familyname, $dateconverterted );
    }

    public function getCompetitorByUid(string $competitorUid , string $currentuser_id): ?CompetitorRepresentation{
        $competitor = $this->repository->getCompetitorByUID($competitorUid);
        return $this->competitorAssembly->toRepresentations(array($competitor),$currentuser_id)[0];

    }

    public function createCompetitor(string $givenName, string $familyName, string $userName, string $birthdate): Competitor {
       return $this->repository->createCompetitor($givenName,$familyName,$userName,$birthdate);
    }

    public function createCredentialFor(string $getId, string $getParticipantUid, string $int, string $int1)
    {
        $this->repository->creatCompetitorCredential($getId,$getParticipantUid,$int,   $int1);
    }

    public function deleteCompetitorCredentialForParticipant($getParticipantUid, $competitor_uid)
    {
        $this->repository->deleteCompetitorCredentialForParticipant($getParticipantUid, $competitor_uid);
    }
}
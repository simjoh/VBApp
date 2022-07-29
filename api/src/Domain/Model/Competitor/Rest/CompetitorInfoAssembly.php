<?php

namespace App\Domain\Model\Competitor\Rest;

use App\Domain\Model\Competitor\CompetitorInfo;

class CompetitorInfoAssembly
{


    public function toRepresentation(CompetitorInfo $competitorInfo,  array $permissions): ?CompetitorInforepresentation {

        $competitorInforeprepresentation = new CompetitorInforepresentation();
        $competitorInforeprepresentation->setAdress($competitorInfo->getAdress());
        $competitorInforeprepresentation->setPlace($competitorInfo->getPlace());
        $competitorInforeprepresentation->setCountry($competitorInfo->getCountry());
        $competitorInforeprepresentation->setEmail($competitorInfo->getEmail());
        $competitorInforeprepresentation->setPhone($competitorInfo->getPhone());

        $linkArray = array();
        $competitorInforeprepresentation->setLinks($linkArray);

        return $competitorInforeprepresentation;

    }

}
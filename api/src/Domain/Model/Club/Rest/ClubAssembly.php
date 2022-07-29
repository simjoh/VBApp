<?php

namespace App\Domain\Model\Club\Rest;

use App\Domain\Model\Club\Club;

class ClubAssembly
{



    public function toRepresentation(Club $club,  array $permissions): ?ClubRepresentation {

        $clubrepr = new ClubRepresentation();

        $clubrepr->setClubUid($club->getClubUid());
        $clubrepr->setTitle($club->getTitle());

        $linkArray = array();
        $clubrepr->setLinks($linkArray);

        return $clubrepr;

    }




}
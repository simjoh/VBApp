<?php

namespace App\Domain\Model\Randonneur\Rest;

use App\common\Rest\Link;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
use App\Domain\Model\Track\Rest\TrackRepresentation;

class RandonneurCheckpointAssembly
{

    public function __construct()
    {

    }



    public function toRepresentation(CheckpointRepresentation $checkpoint, bool $stamped, string $track_uid, string $currentUserUId, string $startnumber, bool $hasDnf): RandonneurCheckPointRepresentation{

       $randonneurcheckpoint =  new RandonneurCheckPointRepresentation();
       $randonneurcheckpoint->setCheckpoint($checkpoint);
//       $randonneurcheckpoint->setTrackInfoRepresentation($trackinfo);
        $linkArray = array();
        if($stamped == false){
            array_push($linkArray, new Link("relation.randonneur.stamp", 'POST', '/api/randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/stamp"));
        } else {
            array_push($linkArray, new Link("relation.randonneur.rollback", 'PUT', '/api/randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/rollback"));
        }
        if($hasDnf == false){
            array_push($linkArray, new Link("relation.randonneur.dnf", 'PUT', '/api/randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/markasdnf"));
        } else {
            array_push($linkArray, new Link("relation.randonneur.dnf.rollback", 'PUT', '/api/randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/rollbackdnf"));
        }

        $randonneurcheckpoint->setLinks($linkArray);
        return $randonneurcheckpoint;
    }

}
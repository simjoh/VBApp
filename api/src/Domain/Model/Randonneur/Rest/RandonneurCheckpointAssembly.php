<?php

namespace App\Domain\Model\Randonneur\Rest;

use App\common\Rest\Link;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
use App\Domain\Model\Track\Rest\TrackRepresentation;
use Psr\Container\ContainerInterface;

class RandonneurCheckpointAssembly
{

    public function __construct(ContainerInterface $c)
    {
        $this->settings = $c->get('settings');
    }



    public function toRepresentation(CheckpointRepresentation $checkpoint, bool $stamped, string $track_uid, string $currentUserUId, string $startnumber, bool $hasDnf, $racepassed): RandonneurCheckPointRepresentation{

       $randonneurcheckpoint =  new RandonneurCheckPointRepresentation();
       $randonneurcheckpoint->setCheckpoint($checkpoint);

       if($racepassed){
           $randonneurcheckpoint->setActive(false);
       } else {
           $randonneurcheckpoint->setActive(true);
       }

//       $randonneurcheckpoint->setTrackInfoRepresentation($trackinfo);
        $linkArray = array();
//        if($racepassed == false){
        if($stamped == false){
            array_push($linkArray, new Link("relation.randonneur.stamp", 'POST', $this->settings['path'] . 'randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/stamp"));
        } else  {
            array_push($linkArray, new Link("relation.randonneur.rollback", 'PUT', $this->settings['path'] . 'randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/rollback"));
        }
        if($hasDnf == false){
            array_push($linkArray, new Link("relation.randonneur.dnf", 'PUT', $this->settings['path'] . 'randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/markasdnf"));
        } else {
            array_push($linkArray, new Link("relation.randonneur.dnf.rollback", 'PUT', $this->settings['path'] . 'randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/rollbackdnf"));
        }

//        }

        $randonneurcheckpoint->setLinks($linkArray);
        return $randonneurcheckpoint;
    }

}
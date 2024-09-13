<?php

namespace App\Domain\Model\Randonneur\Rest;

use App\common\Rest\Link;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
use Psr\Container\ContainerInterface;

class RandonneurCheckpointAssembly
{

    private $settings;
    public function __construct(ContainerInterface $c)
    {
        $this->settings = $c->get('settings');
    }


    public function toRepresentation(CheckpointRepresentation $checkpoint, bool $stamped, string $track_uid, string $currentUserUId, string $startnumber, bool $hasDnf, $racepassed, $stamptime, bool $hasCheckeout): RandonneurCheckPointRepresentation
    {

        $randonneurcheckpoint = new RandonneurCheckPointRepresentation();
        $randonneurcheckpoint->setCheckpoint($checkpoint);

        if ($racepassed) {
            $randonneurcheckpoint->setActive(false);
        } else {
            $randonneurcheckpoint->setActive(true);
        }

        $randonneurcheckpoint->setStamptime($stamptime);

//       $randonneurcheckpoint->setTrackInfoRepresentation($trackinfo);
        $linkArray = array();
//        if($racepassed == false){
        if ($stamped == false) {
            array_push($linkArray, new Link("relation.randonneur.stamp", 'POST', $this->settings['path'] . 'randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/stamp"));
        } else {
            if (!$hasCheckeout) {
                array_push($linkArray, new Link("relation.randonneur.checkout", 'PUT', $this->settings['path'] . 'randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/checkoutFrom"));
            } else {
                array_push($linkArray, new Link("relation.randonneur.undocheckout", 'PUT', $this->settings['path'] . 'randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/undocheckoutFrom"));
            }
         //   array_push($linkArray, new Link("relation.randonneur.rollback", 'PUT', $this->settings['path'] . 'randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/rollback"));
        }
        if ($hasDnf == false) {
            array_push($linkArray, new Link("relation.randonneur.dnf", 'PUT', $this->settings['path'] . 'randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/markasdnf"));
        } else {
            array_push($linkArray, new Link("relation.randonneur.dnf.rollback", 'PUT', $this->settings['path'] . 'randonneur/' . $currentUserUId . "/track/" . $track_uid . "/startnumber/" . $startnumber . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/rollbackdnf"));
        }

//        }

        $randonneurcheckpoint->setLinks($linkArray);
        return $randonneurcheckpoint;
    }


    public function toRepresentationPreview(CheckpointRepresentation $checkpoint, $stamptime, $dnf): RandonneurCheckPointRepresentation
    {

        $randonneurcheckpoint = new RandonneurCheckPointRepresentation();
        $randonneurcheckpoint->setCheckpoint($checkpoint);

        $randonneurcheckpoint->setStamptime($stamptime);

        $linkArray = array();

        $randonneurcheckpoint->setLinks($linkArray);
        return $randonneurcheckpoint;
    }

    public function toRepresentationForAdmin(string $participant_uid, CheckpointRepresentation $checkpoint, bool $stamped, string $track_uid, bool $hasDnf, bool $racepassed, string $stamptime)
    {
        $randonneurcheckpoint = new RandonneurCheckPointRepresentation();
        $randonneurcheckpoint->setCheckpoint($checkpoint);

        if ($racepassed) {
            $randonneurcheckpoint->setActive(false);
        } else {
            $randonneurcheckpoint->setActive(true);
        }

        $randonneurcheckpoint->setStamptime($stamptime);


        $linkArray = array();
        if ($stamped == false) {
            array_push($linkArray, new Link("relation.randonneur.admin.stamp", 'PUT', $this->settings['path'] . 'participant/' . $participant_uid . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/stamp"));
        } else {
            array_push($linkArray, new Link("relation.randonneur.admin.stamp.rollback", 'PUT', $this->settings['path'] . 'participant/' . $participant_uid . "/checkpoint/" . $checkpoint->getCheckpointUid() . "/rollbackstamp"));
        }


        $randonneurcheckpoint->setLinks($linkArray);
        return $randonneurcheckpoint;

    }

}
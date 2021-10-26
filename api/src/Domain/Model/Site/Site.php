<?php

namespace App\Domain\Model\Site;

use App\Domain\Model\User\Role;

class Site implements \JsonSerializable
{

    private string $site_uid;
    private string $place;
    private string $adress;
    private string $location;


    public function __construct($site_uid, $place, $adress, $location)
    {
        $this->site_uid = $site_uid;
        $this->place = $place;
        $this->adress = $adress;
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getSiteUid(): string
    {
        return $this->site_uid;
    }

    /**
     * @return string
     */
    public function getPlace(): string
    {
        return $this->place;
    }

    /**
     * @return string
     */
    public function getAdress(): string
    {
        return $this->adress;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }


    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }
}
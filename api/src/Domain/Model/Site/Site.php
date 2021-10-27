<?php

namespace App\Domain\Model\Site;

use JsonSerializable;

class Site implements JsonSerializable
{

    private string $site_uid;
    private string $place;
    private string $adress;
    private string $location;
    private string $description;



    public function __construct($site_uid, $place, $adress, $description , $location)
    {
        $this->site_uid = $site_uid;
        $this->place = $place;
        $this->adress = $adress;
        $this->location = $location;
        $this->description = $description;
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


    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     */
    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}
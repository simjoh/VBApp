<?php

namespace App\Domain\Model\Site;

use JsonSerializable;
use PrestaShop\Decimal\DecimalNumber;

class Site implements JsonSerializable
{

    private string $site_uid;
    private string $place;
    private string $adress;
    private string $location;
    private string $description;
    private DecimalNumber $lat;
    private DecimalNumber $lng;



    public function __construct(string $site_uid, string $place, $adress, string $description , $location, DecimalNumber $lat, DecimalNumber $lng)
    {

        $this->site_uid = $site_uid;
        $this->place = $place;
        $this->adress = $adress;
        $this->location = $location;
        $this->description = $description;
        $this->lat = $lat;
        $this->lng = $lng;
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

    /**
     * @return string
     */
    public function getLat(): DecimalNumber
    {

        return $this->lat;
    }

    /**
     * @param string $lat
     */
    public function setLat(DecimalNumber $lat): void
    {
        $this->lat = $lat;
    }

    /**
     * @return string
     */
    public function getLng(): DecimalNumber
    {
        return $this->lng;
    }

    /**
     * @param string $lng
     */
    public function setLng(DecimalNumber $lng): void
    {
        $this->lng = $lng;
    }

    public function jsonSerialize() {
        return (object) get_object_vars($this);
    }

}
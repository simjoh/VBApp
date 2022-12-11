<?php

namespace App\Domain\Model\Site;

use JsonSerializable;
use PrestaShop\Decimal\DecimalNumber;

class Site implements JsonSerializable
{

    private string $site_uid = "";
    private string $place;
    private string $adress;
    private ?string $location = "";
    private string $description;
    private string $picture;
    private DecimalNumber $lat;
    private DecimalNumber $lng;



    public function __construct(string $site_uid, string $place, $adress, string $description , $location, DecimalNumber $lat, DecimalNumber $lng, string $picture)
    {

        $this->site_uid = $site_uid;
        $this->place = $place;
        $this->adress = $adress;
        $this->location = $location;
        $this->description = $description;
        $this->lat = $lat;
        $this->lng = $lng;
        $this->picture = $picture;
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

    /**
     * @return string
     */
    public function getPicture(): string
    {
        return $this->picture;
    }

    /**
     * @param string $picture
     */
    public function setPicture(string $picture): void
    {
        $this->picture = $picture;
    }

    /**
     * @param string $site_uid
     */
    public function setSiteUid(string $site_uid): void
    {
        $this->site_uid = $site_uid;
    }

    public function jsonSerialize(): mixed {
        return (object) get_object_vars($this);
    }

}
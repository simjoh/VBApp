<?php

namespace App\Domain\Model\Site\Rest;

use App\common\Rest\Link;
use JsonSerializable;

class SiteRepresentation implements JsonSerializable
{
    private string $site_uid = "";
    private string $place = "";
    private string $adress = "";
    private string $location = "";
    private ?string $image = "";
    private string $description = "";
    private string $lat = "";
    private string $lng = "";
    private string $check_in_distance = "0.90";
    private string $picture = "";
    private ?array $links = [];


    /**
     * @return string
     */
    public function getSiteUid(): string
    {
        return $this->site_uid;
    }

    /**
     * @param string $site_uid
     */
    public function setSiteUid(string $site_uid): void
    {
        $this->site_uid = $site_uid;
    }

    /**
     * @return string
     */
    public function getPlace(): string
    {
        return $this->place;
    }

    /**
     * @param string $place
     */
    public function setPlace(string $place): void
    {
        $this->place = $place;
    }

    /**
     * @return string
     */
    public function getAdress(): string
    {
        return $this->adress;
    }

    /**
     * @param string $adress
     */
    public function setAdress(string $adress): void
    {
        $this->adress = $adress;
    }

    /**
     * @return string
     */
    public function getLocation(): string
    {
        return $this->location;
    }

    /**
     * @param string $location
     */
    public function setLocation(string $location): void
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param string $image
     */
    public function setImage(string $image): void
    {
        $this->image = $image;
    }

    /**
     * @param array $link
     */
    public function setLink(array $link): void
    {
        $this->links = $link;
    }

    /**
     * @return array|null
     */
    public function getLinks(): ?array
    {
        return $this->links;
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
    public function getLat(): string
    {
        return $this->lat;
    }

    /**
     * @param string $lat
     */
    public function setLat(string $lat): void
    {
        $this->lat = $lat;
    }

    /**
     * @return string
     */
    public function getLng(): string
    {
        return $this->lng;
    }

    /**
     * @param string $lng
     */
    public function setLng(string $lng): void
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
     * @return string
     */
    public function getCheckInDistance(): string
    {
        return $this->check_in_distance;
    }

    /**
     * @param string $check_in_distance
     */
    public function setCheckInDistance(string $check_in_distance): void
    {
        $this->check_in_distance = $check_in_distance;
    }

    public function jsonSerialize(): array
    {
        return [
            'site_uid' => $this->site_uid,
            'place' => $this->place,
            'adress' => $this->adress,
            'location' => $this->location,
            'description' => $this->description,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'image' => $this->image,
            'check_in_distance' => $this->check_in_distance,
            'links' => $this->links
        ];
    }
}
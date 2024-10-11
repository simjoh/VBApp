<?php

namespace App\Domain\Model\Competitor;

class CompetitorInfo
{
    private string $uid;
    private string $competitor_uid;
    private string $email;
    private string $phone;
    private string $adress;
    private string $postal_code;
    private string $place;
    private string $country;
    private ?int $country_id;


    /**
     * @return string
     */
    public function getUid(): string
    {
        return $this->uid;
    }

    /**
     * @param string $uid
     */
    public function setUid(string $uid): void
    {
        $this->uid = $uid;
    }

    /**
     * @return string
     */
    public function getCompetitorUid(): string
    {
        return $this->competitor_uid;
    }

    /**
     * @param string $competitor_uid
     */
    public function setCompetitorUid(string $competitor_uid): void
    {
        $this->competitor_uid = $competitor_uid;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @param string $phone
     */
    public function setPhone(string $phone): void
    {
        $this->phone = $phone;
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
    public function getPostalCode(): string
    {
        return $this->postal_code;
    }

    /**
     * @param string $postal_code
     */
    public function setPostalCode(string $postal_code): void
    {
        $this->postal_code = $postal_code;
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
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @param string $country
     */
    public function setCountry(string $country): void
    {
        $this->country = $country;
    }

    public function getCountryId(): int
    {
        return $this->country_id ?? 0;
    }

    public function setCountryId(int $country_id): void
    {
        $this->country_id = $country_id;
    }


}
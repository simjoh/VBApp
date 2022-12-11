<?php

namespace App\Domain\Model\Competitor\Rest;

use JsonSerializable;

class CompetitorRepresentation implements JsonSerializable
{
    private string $given_name;
    private string $family_name;
    private string $birth_date;
    private string $competitor_uid;


    private array $links = [];


    /**
     * @return string
     */
    public function getGivenName(): string
    {
        return $this->given_name;
    }

    /**
     * @param string $given_name
     */
    public function setGivenName(string $given_name): void
    {
        $this->given_name = $given_name;
    }

    /**
     * @return string
     */
    public function getFamilyName(): string
    {
        return $this->family_name;
    }

    /**
     * @param string $family_name
     */
    public function setFamilyName(string $family_name): void
    {
        $this->family_name = $family_name;
    }

    /**
     * @return string
     */
    public function getBirthDate(): string
    {
        return $this->birth_date;
    }

    /**
     * @param string $birth_date
     */
    public function setBirthDate(string $birth_date): void
    {
        $this->birth_date = $birth_date;
    }

    /**
     * @return array
     */
    public function getLinks(): array
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks(array $links): void
    {
        $this->links = $links;
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

    public function jsonSerialize(): mixed
    {
        return (object)get_object_vars($this);
    }

}
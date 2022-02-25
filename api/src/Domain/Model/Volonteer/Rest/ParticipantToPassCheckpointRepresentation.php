<?php

namespace App\Domain\Model\Volonteer\Rest;

use JsonSerializable;

class ParticipantToPassCheckpointRepresentation  implements JsonSerializable
{

    private string $trackUid;
    private string $participantUid;
    private string $siteUid;
    private string $checkpointUid;
    private string $adress;
    private string $startNumber;
    private string $givenName;
    private string $familyName;
    private bool $passed;
    private ?string $passededDateTime = "";
    private bool $dnf;
    private ?array $link;



    /**
     * @return string
     */
    public function getTrackUid(): string
    {
        return $this->trackUid;
    }

    /**
     * @param string $trackUid
     */
    public function setTrackUid(string $trackUid): void
    {
        $this->trackUid = $trackUid;
    }

    /**
     * @return string
     */
    public function getParticipantUid(): string
    {
        return $this->participantUid;
    }

    /**
     * @param string $participantUid
     */
    public function setParticipantUid(string $participantUid): void
    {
        $this->participantUid = $participantUid;
    }

    /**
     * @return string
     */
    public function getSiteUid(): string
    {
        return $this->siteUid;
    }

    /**
     * @param string $siteUid
     */
    public function setSiteUid(string $siteUid): void
    {
        $this->siteUid = $siteUid;
    }

    /**
     * @return string
     */
    public function getCheckpointUid(): string
    {
        return $this->checkpointUid;
    }

    /**
     * @param string $checkpointUid
     */
    public function setCheckpointUid(string $checkpointUid): void
    {
        $this->checkpointUid = $checkpointUid;
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
    public function getStartNumber(): string
    {
        return $this->startNumber;
    }

    /**
     * @param string $startNumber
     */
    public function setStartNumber(string $startNumber): void
    {
        $this->startNumber = $startNumber;
    }

    /**
     * @return string
     */
    public function getGivenName(): string
    {
        return $this->givenName;
    }

    /**
     * @param string $givenName
     */
    public function setGivenName(string $givenName): void
    {
        $this->givenName = $givenName;
    }

    /**
     * @return string
     */
    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    /**
     * @param string $familyName
     */
    public function setFamilyName(string $familyName): void
    {
        $this->familyName = $familyName;
    }

    /**
     * @return bool
     */
    public function isPassed(): bool
    {
        return $this->passed;
    }

    /**
     * @param bool $passed
     */
    public function setPassed(bool $passed): void
    {
        $this->passed = $passed;
    }

    /**
     * @return bool
     */
    public function isDnf(): bool
    {
        return $this->dnf;
    }

    /**
     * @param bool $dnf
     */
    public function setDnf(bool $dnf): void
    {
        $this->dnf = $dnf;
    }

    /**
     * @return string
     */
    public function getPassededDateTime(): string
    {
        return $this->passededDateTime;
    }

    /**
     * @param string $passededDateTime
     */
    public function setPassededDateTime(string $passededDateTime): void
    {
        $this->passededDateTime = $passededDateTime;
    }



    /**
     * @return array|null
     */
    public function getLink(): ?array
    {
        return $this->link;
    }

    /**
     * @param array|null $link
     */
    public function setLink(?array $link): void
    {
        $this->link = $link;
    }

    public function jsonSerialize()
    {
        return (object)get_object_vars($this);
    }

}
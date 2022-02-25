<?php

namespace App\Domain\Model\Volonteer;

class ParticipantToPassCheckpoint
{
    private string $track_uid;
    private string $participant_uid;
    private string $site_uid;
    private string $checkpoint_uid;
    private string $adress;
    private int $startnumber;
    private string $given_name;
    private string $family_name;
    private bool $passed;
    private $passeded_date_time = null;

    /**
     * @return string
     */
    public function getTrackUid(): string
    {
        return $this->track_uid;
    }

    /**
     * @param string $track_uid
     */
    public function setTrackUid(string $track_uid): void
    {
        $this->track_uid = $track_uid;
    }

    /**
     * @return string
     */
    public function getParticipantUid(): string
    {
        return $this->participant_uid;
    }

    /**
     * @param string $participant_uid
     */
    public function setParticipantUid(string $participant_uid): void
    {
        $this->participant_uid = $participant_uid;
    }

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
    public function getCheckpointUid(): string
    {
        return $this->checkpoint_uid;
    }

    /**
     * @param string $checkpoint_uid
     */
    public function setCheckpointUid(string $checkpoint_uid): void
    {
        $this->checkpoint_uid = $checkpoint_uid;
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
     * @return int
     */
    public function getStartnumber(): int
    {
        return $this->startnumber;
    }

    /**
     * @param int $startnumber
     */
    public function setStartnumber(int $startnumber): void
    {
        $this->startnumber = $startnumber;
    }

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
     * @return mixed
     */
    public function getPassededDateTime()
    {
        return $this->passeded_date_time;
    }

    /**
     * @param mixed $passeded_date_time
     */
    public function setPassededDateTime($passeded_date_time): void
    {
        $this->passeded_date_time = $passeded_date_time;
    }
}
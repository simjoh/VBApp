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
    private bool $started;
    private bool $volonteer_checkin;
    private $opens;
    private $closes;

    private bool $dnf;

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
     * @param mixed $passeded_date_time
     */
    public function setPassededDateTime($passeded_date_time): void
    {
        $this->passeded_date_time = $passeded_date_time;
    }

    /**
     * @return bool
     */
    public function isStarted(): bool
    {
        return $this->started;
    }

    /**
     * @param bool $started
     */
    public function setStarted(bool $started): void
    {
        $this->started = $started;
    }

    /**
     * @return bool
     */
    public function isVolonteerCheckin(): bool
    {
        return $this->volonteer_checkin;
    }

    /**
     * @param bool $volonteer_checkin
     */
    public function setVolonteerCheckin(bool $volonteer_checkin): void
    {
        $this->volonteer_checkin = $volonteer_checkin;
    }

    /**
     * @return mixed
     */
    public function getOpens()
    {
        return $this->opens;
    }

    /**
     * @param mixed $opens
     */
    public function setOpens($opens): void
    {
        $this->opens = $opens;
    }

    /**
     * @return mixed
     */
    public function getCloses()
    {
        return $this->closes;
    }

    /**
     * @param mixed $closes
     */
    public function setCloses($closes): void
    {
        $this->closes = $closes;
    }

    /**
     * Sets properties from an associative array of database values
     * 
     * @param array $data Associative array of property values
     * @return void
     */
    public function setProperties(array $data)
    {
        // Initialize all required properties with default values first
        $this->setStartnumber(0);
        $this->setPassed(false);
        $this->setStarted(false);
        $this->setVolonteerCheckin(false);
        $this->setDnf(false);
        
        // Now set properties from the data array
        foreach ($data as $key => $value) {
            // Convert snake_case to camelCase for method names
            $methodName = 'set' . str_replace('_', '', ucwords($key, '_'));
            
            // Special case for startnumber/start_number
            if ($key === 'start_number' || $key === 'startnumber' || $key === 'start_nr') {
                $this->setStartnumber((int)$value);
                continue;
            }
            
            // Special case for boolean fields
            if (in_array($key, ['passed', 'started', 'volonteer_checkin', 'dnf'])) {
                $value = (bool)$value;
            }
            
            // Call the setter method if it exists
            if (method_exists($this, $methodName)) {
                $this->$methodName($value);
            }
        }
    }
}
<?php

namespace App\Domain\Model\Partisipant;

class ParticipantCheckpoint
{
    
    private string $participant_uid;
    private string $checkpoint_uid;
    private bool $passed;
    private  $passeded_date_time;
    private ?string $lat;
    private ?string $lng;
    private bool $volonteer_checkin;
    private $checkout_date_time;
    
    /**
     * @return mixed
     */
    public function getCheckoutDateTime()
    {
        return $this->checkout_date_time;
    }
    
    /**
     * @param mixed $checkout_date_time
     */
    public function setCheckoutDateTime($checkout_date_time): void
    {
        $this->checkout_date_time = $checkout_date_time;
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


}
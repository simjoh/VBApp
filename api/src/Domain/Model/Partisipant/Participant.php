<?php

namespace App\Domain\Model\Partisipant;

class Participant
{

    private ?string $participant_uid = '';
    private string $track_uid;
    private ?string $competitor_uid;
    private string $startnumber;
    private bool $finished;
    private ?string $acpkod;
    private string $club_uid;
    private $time;
    private bool $dns;
    private bool $dnf;
    private ?bool $started;
    private ?bool $medal;



    private ?string $brevenr = null;
    private  $register_date_time;
    private ?string $dns_timestamp = null;
    private ?string $dnf_timestamp = null;
    private ?string $finished_timestamp = null;




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
     * @return string|null
     */
    public function getCompetitorUid(): ?string
    {
        return $this->competitor_uid;
    }

    /**
     * @param string|null $competitor_uid
     */
    public function setCompetitorUid(?string $competitor_uid): void
    {
        $this->competitor_uid = $competitor_uid;
    }

    /**
     * @return string
     */
    public function getStartnumber(): string
    {
        return $this->startnumber;
    }

    /**
     * @param string $startnumber
     */
    public function setStartnumber(string $startnumber): void
    {
        $this->startnumber = $startnumber;
    }

    /**
     * @return bool
     */
    public function isFinished(): bool
    {
        return $this->finished;
    }

    /**
     * @param bool $finished
     */
    public function setFinished(bool $finished): void
    {
        $this->finished = $finished;
    }

    /**
     * @return string
     */
    public function getAcpkod(): string
    {
        return $this->acpkod;
    }

    /**
     * @param string $acpcode
     */
    public function setAcpkod(string $acpkod): void
    {
        $this->acpkod = $acpkod;
    }

    /**
     * @return string
     */
    public function getClubUid(): string
    {
        return $this->club_uid;
    }

    /**
     * @param string $club_uid
     */
    public function setClubUid(string $club_uid): void
    {
        $this->club_uid = $club_uid;
    }

    /**
     * @return mixed
     */
    public function getTime()
    {
        return $this->time;
    }

    /**
     * @param mixed $time
     */
    public function setTime($time): void
    {
        $this->time = $time;
    }

    /**
     * @return bool
     */
    public function isDns(): bool
    {
        return $this->dns;
    }

    /**
     * @param bool $dns
     */
    public function setDns(bool $dns): void
    {
        $this->dns = $dns;
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
    public function getBrevenr(): ?string
    {
        return $this->brevenr;
    }

    /**
     * @param string $brevenr
     */
    public function setBrevenr(string $brevenr): void
    {
        $this->brevenr = $brevenr;
    }

    /**
     * @return mixed
     */
    public function getRegisterDateTime()
    {
        return $this->register_date_time;
    }

    /**
     * @param mixed $register_date_time
     */
    public function setRegisterDateTime($register_date_time): void
    {
        $this->register_date_time = $register_date_time;
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
     * @return bool|null
     */
    public function getMedal(): ?bool
    {
        return $this->medal;
    }

    /**
     * @param bool|null $medal
     */
    public function setMedal(?bool $medal): void
    {
        $this->medal = $medal;
    }

    /**
     * @return string|null
     */
    public function getDnsTimestamp(): ?string
    {
        return $this->dns_timestamp;
    }

    /**
     * @param string|null $dns_timestamp
     */
    public function setDnsTimestamp(?string $dns_timestamp): void
    {
        $this->dns_timestamp = $dns_timestamp;
    }

    /**
     * @return string|null
     */
    public function getDnfTimestamp(): ?string
    {
        return $this->dnf_timestamp;
    }

    /**
     * @param string|null $dnf_timestamp
     */
    public function setDnfTimestamp(?string $dnf_timestamp): void
    {
        $this->dnf_timestamp = $dnf_timestamp;
    }

    /**
     * @return string|null
     */
    public function getFinishedTimestamp(): ?string
    {
        return $this->finished_timestamp;
    }

    /**
     * @param string|null $finished_timestamp
     */
    public function setFinishedTimestamp(?string $finished_timestamp): void
    {
        $this->finished_timestamp = $finished_timestamp;
    }

}
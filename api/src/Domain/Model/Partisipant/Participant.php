<?php

namespace App\Domain\Model\Partisipant;

class Participant
{

    private string $participant_uid;
    private string $track_uid;
    private ?string $competitor_uid;
    private string $startnumber;
    private bool $finished;
    private string $acpcode;
    private string $club_uid;
    private $time;
    private bool $dns;
    private bool $dnf;
    private ?string $brevenr;


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
    public function getAcpcode(): string
    {
        return $this->acpcode;
    }

    /**
     * @param string $acpcode
     */
    public function setAcpcode(string $acpcode): void
    {
        $this->acpcode = $acpcode;
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
    public function getBrevenr(): string
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


}
<?php
namespace App\Domain\Model\Stats;
class TrackStatistics
{

    private int $countParticipants;
    private int $countDnf;
    private int $countDns;
    private int $countFinished;

    /**
     * @return int
     */
    public function getCountParticipants(): int
    {
        return $this->countParticipants;
    }

    /**
     * @param int $countParticipants
     */
    public function setCountParticipants(int $countParticipants): void
    {
        $this->countParticipants = $countParticipants;
    }

    /**
     * @return int
     */
    public function getCountDnf(): int
    {
        return $this->countDnf;
    }

    /**
     * @param int $countDnf
     */
    public function setCountDnf(int $countDnf): void
    {
        $this->countDnf = $countDnf;
    }

    /**
     * @return int
     */
    public function getCountDns(): int
    {
        return $this->countDns;
    }

    /**
     * @param int $countDns
     */
    public function setCountDns(int $countDns): void
    {
        $this->countDns = $countDns;
    }

    /**
     * @return int
     */
    public function getCountFinished(): int
    {
        return $this->countFinished;
    }

    /**
     * @param int $countFinished
     */
    public function setCountFinished(int $countFinished): void
    {
        $this->countFinished = $countFinished;
    }


}
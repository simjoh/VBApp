<?php

namespace App\Domain\Model\Event;


class Event
{
    private string $event_uid;
    private ?string $title;
    private  $start_date;
    private  $end_date;
    private ?bool $active;
    private ?bool $canceled;
    private ?bool $completed;
    private ?string $description;

    /**
     * @return string
     */
    public function getEventUid(): string
    {
        return $this->event_uid;
    }

    /**
     * @param string $event_uid
     */
    public function setEventUid(string $event_uid): void
    {
        $this->event_uid = $event_uid;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return mixed
     */
    public function getStartdate()
    {
        return $this->start_date;
    }

    /**
     * @param mixed $startdate
     */
    public function setStartdate($startdate): void
    {
        $this->start_date = $startdate;
    }

    /**
     * @return mixed
     */
    public function getEnddate()
    {
        return $this->end_date;
    }

    /**
     * @param mixed $enddate
     */
    public function setEnddate($enddate): void
    {
        $this->end_date = $enddate;
    }

    /**
     * @return bool
     */
    public function isActive(): bool
    {
        return $this->active;
    }

    /**
     * @param bool $active
     */
    public function setActive(bool $active): void
    {
        $this->active = $active;
    }

    /**
     * @return bool
     */
    public function isCanceled(): bool
    {
        return $this->canceled;
    }

    /**
     * @param bool $canceled
     */
    public function setCanceled(bool $canceled): void
    {
        $this->canceled = $canceled;
    }

    /**
     * @return bool
     */
    public function isCompleted(): bool
    {
        return $this->completed;
    }

    /**
     * @param bool $completed
     */
    public function setCompleted(bool $completed): void
    {
        $this->completed = $completed;
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


}
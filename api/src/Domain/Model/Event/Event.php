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
    private ?string $description = null;
    private ?int $organizer_id = null;
    private ?string $created_at = null;
    private ?string $updated_at = null;

//    /**
//     * @param string $event_uid
//     * @param string|null $title
//     * @param $start_date
//     * @param $end_date
//     * @param bool|null $active
//     * @param bool|null $canceled
//     * @param bool|null $completed
//     * @param string|null $description
//     * @param int|null $organizer_id
//     * @param string|null $created_at
//     * @param string|null $updated_at
//     */
//    public function __construct(string $event_uid, ?string $title, $start_date, $end_date, ?bool $active, ?bool $canceled, ?bool $completed, ?string $description, ?int $organizer_id = null, ?string $created_at = null, ?string $updated_at = null)
//    {
//        $this->event_uid = $event_uid;
//        $this->title = $title;
//        $this->start_date = $start_date;
//        $this->end_date = $end_date;
//        $this->active = $active;
//        $this->canceled = $canceled;
//        $this->completed = $completed;
//        $this->description = $description;
//        $this->organizer_id = $organizer_id;
//        $this->created_at = $created_at;
//        $this->updated_at = $updated_at;
//    }


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
     * @return string|null
     */
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param string|null $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return int|null
     */
    public function getOrganizerId(): ?int
    {
        return $this->organizer_id;
    }

    /**
     * @param int|null $organizer_id
     */
    public function setOrganizerId(?int $organizer_id): void
    {
        $this->organizer_id = $organizer_id;
    }

    /**
     * @return string|null
     */
    public function getCreatedAt(): ?string
    {
        return $this->created_at;
    }

    /**
     * @param string|null $created_at
     */
    public function setCreatedAt(?string $created_at): void
    {
        $this->created_at = $created_at;
    }

    /**
     * @return string|null
     */
    public function getUpdatedAt(): ?string
    {
        return $this->updated_at;
    }

    /**
     * @param string|null $updated_at
     */
    public function setUpdatedAt(?string $updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}
<?php

namespace App\Domain\Model\Track;



class Track
{
   public string $track_uid;
   private string $title;
   private ?string $heightdifference;
   private string $event_uid;
   private ?string $description;
   private ?string $link = "";
   private ?int $organizer_id = null;
   private ?string $distance;
   private bool $active;
   private array $checkpoints = [];
   private  $start_date_time = "";


    /**
     * @param string $track_uid
     * @param string $title
     * @param string $heightdifference
     * @param string $event_uid
     * @param string $description
     * @param string $distance
     * @param array $checkpoints
     */
//    public function __construct(string $track_uid, string $title, string $heightdifference, string $event_uid, string $description, string $distance)
//    {
//        $this->track_uid = $track_uid;
//        $this->title = $title;
//        $this->heightdifference = $heightdifference;
//        $this->event_uid = $event_uid;
//        $this->description = $description;
//        $this->distance = $distance;
////        $this->checkpoints = $checkpoints;
//    }




    /**
     * @return array
     */
    public function getCheckpoints(): array
    {
        return $this->checkpoints;
    }

    /**
     * @param array $checkpoints
     */
    public function setCheckpoints(array $checkpoints): void
    {
        $this->checkpoints = $checkpoints;
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
     * @return string
     */
    public function getHeightdifference(): string
    {
        return $this->heightdifference;
    }

    /**
     * @param string $heightdifference
     */
    public function setHeightdifference(string $heightdifference): void
    {
        $this->heightdifference = $heightdifference;
    }

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

    /**
     * @return string
     */
    public function getDistance(): string
    {
        return $this->distance;
    }

    /**
     * @param string $distance
     */
    public function setDistance(string $distance): void
    {
        $this->distance = $distance;
    }

    /**
     * @return string
     */
    public function getLink(): string
    {
        return $this->link;
    }

    /**
     * @param string $link
     */
    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    /**
     * @return mixed
     */
    public function getStartDateTime()
    {
        return $this->start_date_time;
    }

    /**
     * @param mixed $start_date_time
     */
    public function setStartDateTime($start_date_time): void
    {
        $this->start_date_time = $start_date_time;
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

}

<?php

namespace App\Domain\Model\Loppservice\Rest\Event;

class Event {
    public $title;
    public $description;
    public $startdate;
    public $enddate;
    public $event_type;
    public $organizer_id;
    public $county_id;
    public $event_group_uid;
    public $eventconfig;
    public $products;
    public $route_detail;



    function __construct($title, $description, $startdate, $enddate, $event_type, $organizer_id, $county_id, $event_group_uid, $eventconfig, $products, $route_detail) {
        $this->title = $title;
        $this->description = $description;
        $this->startdate = $startdate;
        $this->enddate = $enddate;
        $this->event_type = $event_type;
        $this->organizer_id = $organizer_id;
        $this->county_id = $county_id;
        $this->event_group_uid = $event_group_uid;
        $this->eventconfig = $eventconfig;
        $this->products = $products;
        $this->route_detail = $route_detail;
    }
}

class EventConfig {
    public $registration_opens;
    public $registration_closes;
    public $max_registrations;
    public $reservation_on_event;
    public $startnumberconfig;

    function __construct($registration_opens, $registration_closes, $max_registrations, $reservation_on_event, $startnumberconfig) {
        $this->registration_opens = $registration_opens;
        $this->registration_closes = $registration_closes;
        $this->max_registrations = $max_registrations;
        $this->reservation_on_event = $reservation_on_event;
        $this->startnumberconfig = $startnumberconfig;
    }
}

class StartNumberConfig {
    public $begins_at;
    public $ends_at;
    public $increment;

    function __construct($begins_at, $ends_at, $increment) {
        $this->begins_at = $begins_at;
        $this->ends_at = $ends_at;
        $this->increment = $increment;
    }
}

class RouteDetail {
    public $distance;
    public $height_difference;
    public $start_time;
    public $start_place;
    public $name;
    public $description;
    public $track_link;

    function __construct($distance, $height_difference, $start_time, $start_place, $name, $description, $track_link) {
        $this->distance = $distance;
        $this->height_difference = $height_difference;
        $this->start_time = $start_time;
        $this->start_place = $start_place;
        $this->name = $name;
        $this->description = $description;
        $this->track_link = $track_link;
    }
}

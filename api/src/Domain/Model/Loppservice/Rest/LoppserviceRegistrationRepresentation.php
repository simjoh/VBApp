<?php

namespace App\Domain\Model\Loppservice\Rest;

use JsonSerializable;

class LoppserviceRegistrationRepresentation implements JsonSerializable
{

    public $registration_uid;
    public $course_uid;
    public $additional_information;
    public $reservation;
    public $reservation_valid_until;
    public $startnumber;
    public $club_uid;
    public $created_at;
    public $updated_at;
    public $ref_nr;
    public $person_uid;
    public $registration;
    public $contactinformation;
    
    // Properties for addparticipant payload
    public $participant;
    public $event_uid;
    public $club;
    public $response_uid;
    public $medal;


    public function jsonSerialize(): mixed
    {
        return (object)get_object_vars($this);
    }

}
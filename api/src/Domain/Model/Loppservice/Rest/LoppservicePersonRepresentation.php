<?php

namespace App\Domain\Model\Loppservice\Rest;

use App\Models\Contactinformation;
use JsonSerializable;
use Symfony\Component\Mime\Address;

class LoppservicePersonRepresentation implements JsonSerializable {
    public string $person_uid;
    public string $firstname;
    public string $surname;
    public string $birthdate;
    public string $registration_registration_uid;
    public string $created_at;
    public string $updated_at;
    public string $checksum;
    public LoppserviceAdressRepresentation $address;
    public LoppserviceContactinformationRepresentation $contactinformation;
    public array $registration;
    public string $response_uid;
    public bool $medal;
    
    // Additional properties for addparticipant payload
    public $participant;
    public $event_uid;
    public $club;

    public function __construct() {

    }

//    public function __construct(array $personInfo) {
//        $this->person_uid = $personInfo['person_uid'];
//        $this->firstname = $personInfo['firstname'];
//        $this->surname = $personInfo['surname'];
//        $this->birthdate = $personInfo['birthdate'];
//        $this->registration_registration_uid = $personInfo['registration_registration_uid'];
//        $this->created_at = $personInfo['created_at'];
//        $this->updated_at = $personInfo['updated_at'];
//        $this->checksum = $personInfo['checksum'];
//
//        // Address
//        $this->address = new LoppserviceAdressRepresentation($personInfo['adress']);
//
//        // Contact Information
//        $this->contact_information = new LoppserviceContactinformationRepresentation($personInfo['contactinformation']);
//
//        // Registrations
//        $this->registrations = [];
//        foreach ($personInfo['registration'] as $registration) {
//            $this->registrations[] = new LoppserviceRegistrationRepresentation($registration);
//        }
//    }

    public function jsonSerialize(): mixed {
        return (object)get_object_vars($this);
    }
}
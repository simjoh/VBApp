<?php

namespace App\Domain\Model\Loppservice\Rest;

use JsonSerializable;

class LoppserviceAdressRepresentation implements JsonSerializable
{
    public $adress_uid;
    public $address;
    public $person_person_uid;
    public $postal_code;
    public $city;
    public $country_id;
    public $created_at;
    public $updated_at;

//    public function __construct($addressInfo)
//    {
//        $this->adress_uid = $addressInfo['adress_uid'];
//        $this->address = $addressInfo['adress'];
//        $this->person_person_uid = $addressInfo['person_person_uid'];
//        $this->postal_code = $addressInfo['postal_code'];
//        $this->city = $addressInfo['city'];
//        $this->country_id = $addressInfo['country_id'];
//        $this->created_at = $addressInfo['created_at'];
//        $this->updated_at = $addressInfo['updated_at'];
//    }

    public function jsonSerialize(): mixed
    {
        return (object)get_object_vars($this);
    }
}
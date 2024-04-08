<?php

namespace App\Domain\Model\Loppservice\Rest;

use JsonSerializable;

class LoppserviceContactinformationRepresentation implements JsonSerializable
{
    public $contactinformation_uid;
    public $tel = "";
    public $email = "";
    public $person_person_uid;
    public $created_at;
    public $updated_at;

    public function __construct() {

    }

    public function jsonSerialize(): mixed
    {
        return (object)get_object_vars($this);
    }

}
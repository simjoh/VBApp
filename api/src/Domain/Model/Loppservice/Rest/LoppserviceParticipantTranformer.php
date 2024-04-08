<?php

namespace App\Domain\Model\Loppservice\Rest;

use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class LoppserviceParticipantTranformer implements Transformer
{
    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('address', 'address', LoppserviceAdressRepresentation::class));
        $classBindings->register(new FieldBinding('contactinformation', 'contactinformation', LoppserviceContactinformationRepresentation::class));
        $classBindings->register(new FieldBinding('clubRepresentation', 'clubRepresentation', LoppserviceRegistrationRepresentation::class));
    }

    public function transforms()
    {
        return LoppservicePersonRepresentation::class;
    }

}


<?php

namespace App\Domain\Model\Loppservice\Rest;

use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class LoppserviceRegistrationTransformer  implements Transformer
{

    public function register(ClassBindings $classBindings)
    {
        // TODO: Implement register() method.
        $classBindings->register(new ClassBindings(LoppserviceAdressRepresentation::class));
    }

    public function transforms()
    {
        return LoppserviceRegistrationRepresentation::class;
    }
}
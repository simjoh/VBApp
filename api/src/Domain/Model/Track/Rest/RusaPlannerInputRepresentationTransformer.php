<?php

namespace App\Domain\Model\Track\Rest;

use App\Domain\Model\Site\Rest\SiteRepresentation;
use Karriere\JsonDecoder\Bindings\ArrayBinding;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class RusaPlannerInputRepresentationTransformer implements Transformer
{

    public function register(ClassBindings $classBindings)
    {
//        $classBindings->register(new FieldBinding('SITE', 'SITE', SiteRepresentation::class));
        $classBindings->register(new ArrayBinding("controls", "controls", RusaPlannerControlInputRepresentation::class));
    }

    public function transforms()
    {
        return RusaPlannerInputRepresentation::class;
    }
}
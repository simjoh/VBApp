<?php

namespace App\Domain\Model\Track\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use App\Domain\Model\Site\Site;
use Karriere\JsonDecoder\Bindings\ArrayBinding;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\Bindings\RawBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class RusaPlannerResponseRepresentationTransformer implements Transformer
{

    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding("rusaMetaRepresentation", "rusaMetaRepresentation", RusaMetaRepresentation::class));
        $classBindings->register(new FieldBinding("eventRepresentation", "eventRepresentation", EventRepresentation::class));

        $classBindings->register(new FieldBinding("rusaControlRepresentation", "rusaControlRepresentation", RusaControlRepresentation::class));
        $classBindings->register(new FieldBinding('siteRepresentation', 'siteRepresentation', SiteRepresentation::class));
        $classBindings->register(new FieldBinding("rusaTrackRepresentation", "rusaTrackRepresentation", RusaTrackRepresentation::class));
        $classBindings->register(new ArrayBinding("rusaplannercontrols", "rusaplannercontrols", RusaControlResponseRepresentation::class));
        $classBindings->register(new FieldBinding('link', 'link', Link::class));
    }

    public function transforms()
    {
        return RusaPlannerResponseRepresentation::class;
    }
}
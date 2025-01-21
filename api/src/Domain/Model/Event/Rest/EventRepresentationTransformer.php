<?php

namespace App\Domain\Model\Event\Rest;

use App\common\Rest\Link;
use App\Domain\Model\CheckPoint\Rest\CheckpointRepresentation;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class EventRepresentationTransformer implements Transformer
{

    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('link', 'link', Link::class));
    }

    public function transforms()
    {
        return EventRepresentation::class;
    }
}
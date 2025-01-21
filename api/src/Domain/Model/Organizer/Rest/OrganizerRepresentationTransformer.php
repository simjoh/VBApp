<?php

namespace App\Domain\Model\Organizer\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Event\Rest\EventRepresentation;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class OrganizerRepresentationTransformer implements Transformer
{

    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('link', 'link', Link::class));
    }

    public function transforms()
    {
        return OrganizerRepresentation::class;
    }
}
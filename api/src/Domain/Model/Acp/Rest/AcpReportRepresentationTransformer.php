<?php

namespace App\Domain\Model\Acp\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Event\Rest\EventRepresentation;
use App\Domain\Model\Organizer\Rest\OrganizerRepresentation;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class AcpReportRepresentationTransformer implements Transformer
{

    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('link', 'link', Link::class));
    }

    public function transforms()
    {
        return AcpReportRepresentation::class;
    }
}
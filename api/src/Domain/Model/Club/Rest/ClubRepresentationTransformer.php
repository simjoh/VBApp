<?php

namespace App\Domain\Model\Club\Rest;

use App\common\Rest\Link;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class ClubRepresentationTransformer implements Transformer
{

    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('link', 'link', Link::class));
    }
    public function transforms()
    {
        return ClubRepresentation::class;
    }
}
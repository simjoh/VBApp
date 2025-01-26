<?php

namespace App\Domain\Model\Cache;

use App\common\Rest\Link;
use App\Domain\Model\Club\Rest\ClubRepresentation;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class SvgCacheTransformer implements Transformer
{

    public function register(ClassBindings $classBindings)
    {
        // TODO: Implement register() method.
        $classBindings->register(new FieldBinding('link', 'link', Link::class));
    }

    public function transforms()
    {
        // TODO: Implement transforms() method.

        return CacheRepresentation::class;
    }
}
<?php

namespace App\Domain\Model\CheckPoint\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class CheckpointRepresentationTranformer implements Transformer
{
    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('link', 'link', Link::class));
        $classBindings->register(new FieldBinding('site', 'site', SiteRepresentation::class));
    }
    public function transforms()
    {
        return CheckpointRepresentation::class;
    }
}
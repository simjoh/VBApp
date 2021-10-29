<?php

namespace App\Domain\Model\Checkpoint\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Site\Site;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class CheckpointRepresentationTranformer implements Transformer
{
    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('link', 'link', Link::class));
        $classBindings->register(new FieldBinding('site', 'site', Site::class));
    }
    public function transforms()
    {
        return CheckpointRepresentation::class;
    }
}
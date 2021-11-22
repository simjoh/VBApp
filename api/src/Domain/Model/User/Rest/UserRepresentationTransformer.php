<?php

namespace App\Domain\Model\User\Rest;

use App\common\Rest\Link;
use App\Domain\Model\Site\Rest\SiteRepresentation;
use Karriere\JsonDecoder\Bindings\FieldBinding;
use Karriere\JsonDecoder\ClassBindings;
use Karriere\JsonDecoder\Transformer;

class UserRepresentationTransformer implements Transformer
{

    public function register(ClassBindings $classBindings)
    {
        $classBindings->register(new FieldBinding('link', 'link', Link::class));
        $classBindings->register(new FieldBinding('userInfoRepresentation', 'userInfoRepresentation', UserInfoRepresentation::class));
    }
    public function transforms()
    {
        return UserRepresentation::class;
    }

}
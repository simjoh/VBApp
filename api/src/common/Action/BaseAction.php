<?php

namespace App\common\Action;

use App\Domain\Model\User\User;
use Karriere\JsonDecoder\JsonDecoder;
use ReflectionClass;


class BaseAction
{
    function json_encode_private($object) {
//
//        $public = [];
//        $reflection = new ReflectionClass($object);
//        foreach ($reflection->getProperties() as $property) {
//            $property->setAccessible(true);
//            $public[$property->getName()] = $property->getValue($object);
//        }
       return json_encode($object,JSON_UNESCAPED_SLASHES);



    }

    function getJsonDecoder(): JsonDecoder {
        $jsonDecoder = new JsonDecoder();
        $jsonDecoder->scanAndRegister(User::class);
        return $jsonDecoder;
    }


}
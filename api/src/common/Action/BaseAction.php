<?php

namespace App\common\Action;

use ReflectionClass;


class BaseAction
{
    // behöver komma åt privata klassmedlemmar för att göra om till Json
     function json_encode_private($object) {
        $public = [];
        $reflection = new ReflectionClass($object);
        foreach ($reflection->getProperties() as $property) {
            $property->setAccessible(true);
            $public[$property->getName()] = $property->getValue($object);
        }
        return json_encode($public,JSON_UNESCAPED_SLASHES);
    }
}
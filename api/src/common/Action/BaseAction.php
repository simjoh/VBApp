<?php

namespace App\common\Action;

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


}
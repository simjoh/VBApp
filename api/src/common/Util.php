<?php

namespace App\common;


 use App\common\Exceptions\BrevetException;

 final class Util
{

    public static function nullOrEmpty($str){
        $str = null;
        if(empty($str) || !isset($str)){
            throw new BrevetException("id is reqirered", 2);
        }

 }



}
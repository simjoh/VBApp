<?php

namespace App\common;

 final class Util
{

    public static function nullOrEmpty($str){
        $str = null;
        if(empty($str) || !isset($str)){
            return True;
        }
        return False;
 }

     public static function uuid2bin($uuid) {
         return hex2bin(str_replace('-', '', $uuid));
     }

     public static function bin2uuid($value) {
         $string = bin2hex($value);
         return preg_replace('/([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})/', '$1-$2-$3-$4-$5', $string);
     }

     function get_current_time() {
         return date("H:i");
     }
}
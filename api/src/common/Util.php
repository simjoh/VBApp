<?php

namespace App\common;

use DateTime;
use DateTimeZone;

final class Util
{

    public static function nullOrEmpty($str)
    {
        $str = null;
        if (empty($str) || !isset($str)) {
            return True;
        }
        return False;
    }

    public static function uuid2bin($uuid)
    {
        return hex2bin(str_replace('-', '', $uuid));
    }

    public static function bin2uuid($value)
    {
        $string = bin2hex($value);
        return preg_replace('/([0-9a-f]{8})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{4})([0-9a-f]{12})/', '$1-$2-$3-$4-$5', $string);
    }

    function get_current_time()
    {
        return date("H:i");
    }


    static function secToHR($seconds)
    {
        $hoursminsandsecs = new DateTime($seconds, new DateTimeZone("Europe/Stockholm"));
        return $hoursminsandsecs;
        // return gmdate("H:i", $seconds);;
    }

    static function calculateSecondsBetween($date): string
    {
        $datetime1 = new DateTime($date, new DateTimeZone("Europe/Stockholm"));
        $datetime2 = new DateTime(date('Y-m-d H:i:s'), new DateTimeZone("Europe/Stockholm"));

        $interval = $datetime1->diff($datetime2);
        $d = $interval->format("%d");
        $hours = $interval->format("%h");
        $minutes = $interval->format("%I");

//        if ($minutes == "0") {
//            $minutes = $minutes . "0";
//        }
        return $d * 24 + $hours . ":" . $minutes;
    }
}
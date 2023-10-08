<?php

namespace App\Traits;


use App\Models\Registration;
use App\Models\StartNumberConfig;

trait StartnumberTrait
{
    public function getStartnumber(string $course_uid, StartNumberConfig $startNumberConfig): int
    {
        $current_max = Registration::where('course_uid', $course_uid)->max('startnumber');
        if ($current_max == null) {
            return $startNumberConfig->begins_at;
        }
        $startnumbers = Registration::where('course_uid', $course_uid)->whereNotNull('startnumber')->pluck('startnumber')->values();
        $arrwithstartnumbers = collect($startnumbers);
        $missingnumber = array_diff(range($startNumberConfig->begins_at, $current_max), $arrwithstartnumbers->toArray());
        $valtogetfromarray = array_key_first($missingnumber);
        if ($valtogetfromarray) {
            return $missingnumber[$valtogetfromarray];
        }
        return $current_max + $startNumberConfig->increments;
    }
}

<?php


namespace App\Traits;



use App\Enums\Months;
use Illuminate\Support\Facades\App;

trait DaysTrait
{
    public function daysforSelect(): array
    {
        $days = array();
        for ($x = 1; $x <= 31; $x++) {
            $days[$x] =   $day = sprintf("%02d", $x);
        }

        return $days;
    }
}

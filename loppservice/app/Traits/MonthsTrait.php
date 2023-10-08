<?php


namespace App\Traits;


use App\Enums\Months;
use Illuminate\Support\Facades\App;

trait MonthsTrait
{
    public function monthsforSelect(): array
    {
        $months = array();
        $val = 1;
        foreach (Months::cases() as $shape) {
            $months[$val] = Months::getLabel(Months::tryFrom($shape->value))[App::getLocale()];
            $val++;
        }
        return $months;
    }
}

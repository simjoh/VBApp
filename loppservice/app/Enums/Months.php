<?php

namespace App\Enums;

enum Months: string
{

    case JANUARI = 'January';
    case FEBRUARI = 'Februari';
    case MARCH = 'March';
    case APRIL = 'April';
    case MAY = 'May';
    case JUNE = 'June';
    case JULY = 'July';
    case AUGUST = 'August';
    case SEPTEMBER = 'September';
    case OCTOBER = 'October';
    case NOVEMBER = 'November';
    case DECEMBER = 'December';


    public function label(): array
    {
        return static::getLabel($this);
    }

    public static function getAllValues(): array
    {
        return array_column(Months::cases(), 'value');
    }

    public static function getLabel(self $value): array
    {
        return match ($value) {
            Months::JANUARI => ['sv' => 'januari', 'en' => date("F", mktime(0, 0, 0, 1, 10)), 'ord' => "01"],
            Months::FEBRUARI => ['sv' => 'Februari', 'en' => date("F", mktime(0, 0, 0, 2, 10)), 'ord' =>  "02"],
            Months::MARCH => ['sv' => 'Mars', 'en' => date("F", mktime(0, 0, 0, 3, 10)), 'ord' => "03"],
            Months::APRIL => ['sv' => 'April', 'en' => date("F", mktime(0, 0, 0, 4, 10)), 'ord' => "04"],
            Months::MAY => ['sv' => 'Maj', 'en' => date("F", mktime(0, 0, 0, 5, 10)), 'ord' => "05"],
            Months::JUNE => ['sv' => 'Juni', 'en' => date("F", mktime(0, 0, 0, 6, 10)), 'ord' => "06"],
            Months::JULY => ['sv' => 'Juli', 'en' => date("F", mktime(0, 0, 0, 7, 10)), 'ord' => "07"],
            Months::AUGUST => ['sv' => 'Augusti', 'en' => date("F", mktime(0, 0, 0, 8, 10)), 'ord' => "08"],
            Months::SEPTEMBER => ['sv' => 'September', 'en' => date("F", mktime(0, 0, 0, 9, 10)), 'ord' => "09"],
            Months::OCTOBER => ['sv' => 'Oktober', 'en' => date("F", mktime(0, 0, 0, 10, 10)), 'ord' => "10"],
            Months::NOVEMBER => ['sv' => 'November', 'en' => date("F", mktime(0, 0, 0, 11, 10)), 'ord' => "11"],
            Months::DECEMBER => ['sv' => 'December', 'en' => date("F", mktime(0, 0, 0, 12, 10)), 'ord' => "12"],
        };
    }

}

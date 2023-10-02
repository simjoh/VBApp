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
            Months::JANUARI => ['sv' => 'januari', 'en' => date("F", mktime(0, 0, 0, 1, 10)), 'ord' => str_pad(1, 2, '0', STR_PAD_LEFT)],
            Months::FEBRUARI => ['sv' => 'Februari', 'en' => date("F", mktime(0, 0, 0, 2, 10)), 'ord' =>  str_pad(2, 2, '0', STR_PAD_LEFT)],
            Months::MARCH => ['sv' => 'Mars', 'en' => date("F", mktime(0, 0, 0, 3, 10)), 'ord' => str_pad(3, 2, '0', STR_PAD_LEFT)],
            Months::APRIL => ['sv' => 'April', 'en' => date("F", mktime(0, 0, 0, 4, 10)), 'ord' => str_pad(4, 2, '0', STR_PAD_LEFT)],
            Months::MAY => ['sv' => 'Maj', 'en' => date("F", mktime(0, 0, 0, 5, 10)), 'ord' => str_pad(5, 2, '0', STR_PAD_LEFT)],
            Months::JUNE => ['sv' => 'Juni', 'en' => date("F", mktime(0, 0, 0, 6, 10)), 'ord' => str_pad(6, 2, '0', STR_PAD_LEFT)],
            Months::JULY => ['sv' => 'Juli', 'en' => date("F", mktime(0, 0, 0, 7, 10)), 'ord' => str_pad(7, 2, '0', STR_PAD_LEFT)],
            Months::AUGUST => ['sv' => 'Augusti', 'en' => date("F", mktime(0, 0, 0, 8, 10)), 'ord' => str_pad(8, 2, '0', STR_PAD_LEFT)],
            Months::SEPTEMBER => ['sv' => 'September', 'en' => date("F", mktime(0, 0, 0, 9, 10)), 'ord' => str_pad(9, 2, '0', STR_PAD_LEFT)],
            Months::OCTOBER => ['sv' => 'Oktober', 'en' => date("F", mktime(0, 0, 0, 10, 10)), 'ord' =>  str_pad(0, 2, '1', STR_PAD_LEFT)],
            Months::NOVEMBER => ['sv' => 'November', 'en' => date("F", mktime(0, 0, 0, 11, 10)), 'ord' =>  str_pad(1, 2, '1', STR_PAD_LEFT)],
            Months::DECEMBER => ['sv' => 'December', 'en' => date("F", mktime(0, 0, 0, 12, 10)), 'ord' => str_pad(2, 2, '1', STR_PAD_LEFT)],
        };
    }

}

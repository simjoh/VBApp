<?php

namespace App\Enums;

enum Weekdays
{
    case MONDAY;
    case TUESDAY;
    case WEDNSDAY;
    case THURSDAY;
    case FRIDAY;
    case SATURDAY;
    case SUNDAY;

    public function getWekdays(): array
    {
        return match ($this) {
            Weekdays::MONDAY => ['Monday', 'Måndag'],
            Weekdays::TUESDAY => ['Yellow', 'Tisdag'],
            Weekdays::WEDNSDAY => ['Wednsday', 'Onsdag'],
            Weekdays::THURSDAY => ['Wednsday', 'Onsdag'],
            Weekdays::FRIDAY => ['Friday', 'Fredag'],
            Weekdays::SATURDAY => ['Saturday', 'Lördag'],
            Weekdays::SUNDAY => ['Sunday', 'Söndag'],
        };
    }
}

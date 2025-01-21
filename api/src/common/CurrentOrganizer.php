<?php

namespace App\common;

use App\Domain\Model\Organizer\Organizer;

class CurrentOrganizer
{


    private static ?Organizer $organizer = null;

    public static function setUser($organizer): void
    {

        if ($organizer === null) {
            self::$organizer = null; // Reset user
            return;
        }

        // Set the user
        self::$organizer = $organizer;

    }

    public static function getUser()
    {
        return self::$organizer;
    }
}
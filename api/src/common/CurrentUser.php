<?php

namespace App\common;

use App\Domain\Model\User\User;

class CurrentUser
{
    private static ?User $user;

    public static function setUser($user): void
    {
        if ($user === null) {
            self::$user = null; // Reset user
            return;
        }
        // Set the user
        self::$user = $user;
    }

    public static function getUser()
    {
        return self::$user;
    }
}
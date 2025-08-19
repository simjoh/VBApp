<?php

namespace App\common\Service;

class ScheduleHelper
{
    /**
     * Check if command should run every minute
     * @return bool
     */
    public static function everyMinute(): bool
    {
        return true; // Always run
    }

    /**
     * Check if command should run every X minutes
     * @param int $minutes
     * @return bool
     */
    public static function everyMinutes(int $minutes): bool
    {
        return (int)date('i') % $minutes === 0;
    }

    /**
     * Check if command should run every hour
     * @return bool
     */
    public static function everyHour(): bool
    {
        return (int)date('i') === 0;
    }

    /**
     * Check if command should run every X hours
     * @param int $hours
     * @return bool
     */
    public static function everyHours(int $hours): bool
    {
        $currentHour = (int)date('H');
        return $currentHour % $hours === 0 && (int)date('i') === 0;
    }

    /**
     * Check if command should run daily at specific time
     * @param int $hour
     * @param int $minute
     * @return bool
     */
    public static function daily(int $hour, int $minute = 0): bool
    {
        $currentHour = (int)date('H');
        $currentMinute = (int)date('i');
        return $currentHour === $hour && $currentMinute >= $minute && $currentMinute < ($minute + 5);
    }

    /**
     * Check if command should run weekly on specific day and time
     * @param int $dayOfWeek (0 = Sunday, 1 = Monday, ..., 6 = Saturday)
     * @param int $hour
     * @param int $minute
     * @return bool
     */
    public static function weekly(int $dayOfWeek, int $hour, int $minute = 0): bool
    {
        $currentDay = (int)date('w');
        $currentHour = (int)date('H');
        $currentMinute = (int)date('i');
        return $currentDay === $dayOfWeek && $currentHour === $hour && $currentMinute >= $minute && $currentMinute < ($minute + 5);
    }

    /**
     * Check if command should run monthly on specific day and time
     * @param int $dayOfMonth (1-31)
     * @param int $hour
     * @param int $minute
     * @return bool
     */
    public static function monthly(int $dayOfMonth, int $hour, int $minute = 0): bool
    {
        $currentDay = (int)date('j');
        $currentHour = (int)date('H');
        $currentMinute = (int)date('i');
        return $currentDay === $dayOfMonth && $currentHour === $hour && $currentMinute >= $minute && $currentMinute < ($minute + 5);
    }

    /**
     * Check if command should run at specific time
     * @param string $time Format: 'HH:MM' or 'HH:MM:SS'
     * @return bool
     */
    public static function at(string $time): bool
    {
        $timeParts = explode(':', $time);
        $targetHour = (int)$timeParts[0];
        $targetMinute = (int)$timeParts[1];
        
        $currentHour = (int)date('H');
        $currentMinute = (int)date('i');
        
        return $currentHour === $targetHour && $currentMinute >= $targetMinute && $currentMinute < ($targetMinute + 5);
    }

    /**
     * Check if command should run between specific hours
     * @param int $startHour
     * @param int $endHour
     * @return bool
     */
    public static function between(int $startHour, int $endHour): bool
    {
        $currentHour = (int)date('H');
        return $currentHour >= $startHour && $currentHour < $endHour;
    }

    /**
     * Check if command should run on weekdays only
     * @param int $hour
     * @param int $minute
     * @return bool
     */
    public static function weekdays(int $hour, int $minute = 0): bool
    {
        $currentDay = (int)date('w');
        $currentHour = (int)date('H');
        $currentMinute = (int)date('i');
        
        // Weekdays are 1-5 (Monday to Friday)
        return $currentDay >= 1 && $currentDay <= 5 && $currentHour === $hour && $currentMinute >= $minute && $currentMinute < ($minute + 5);
    }

    /**
     * Check if command should run on weekends only
     * @param int $hour
     * @param int $minute
     * @return bool
     */
    public static function weekends(int $hour, int $minute = 0): bool
    {
        $currentDay = (int)date('w');
        $currentHour = (int)date('H');
        $currentMinute = (int)date('i');
        
        // Weekends are 0 and 6 (Sunday and Saturday)
        return ($currentDay === 0 || $currentDay === 6) && $currentHour === $hour && $currentMinute >= $minute && $currentMinute < ($minute + 5);
    }
} 
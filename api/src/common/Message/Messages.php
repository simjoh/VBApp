<?php

namespace App\common\Message;


class Messages
{
    // Prevent instantiation
    private function __construct()
    {
    }

    // Define messages in different languages
    private static array $messages = [
        'en' => [
            'WELCOME' => 'Welcome to eBrevet!',
            'ERROR_404' => 'Page not found.',
            'ERROR_500' => 'Internal server Error.',
            'LOGIN_SUCCESS' => 'Login successful!',
            'LOGIN_FAILURE' => 'Invalid username or password.',
            'LOGOUT' => 'You have been logged out.',
        ],
        'sv' => [
            'WELCOME' => 'Välkommen till eBrevet!',
            'ERROR_404' => 'Sidan hittades inte.',
            'ERROR_500' => 'Internt server fel',
            'LOGIN_SUCCESS' => 'Inloggning lyckades!',
            'LOGIN_FAILURE' => 'Ogiltigt användarnamn eller lösenord.',
            'LOGOUT' => 'Du har loggats ut.',
        ],
        'de' => [
            'WELCOME' => 'Välkommen till eBrevet!',
            'ERROR_404' => 'Sidan hittades inte.',
            'ERROR_500' => 'Internt server fel',
            'LOGIN_SUCCESS' => 'Inloggning lyckades!',
            'LOGIN_FAILURE' => 'Ogiltigt användarnamn eller lösenord.',
            'LOGOUT' => 'Du har loggats ut.',
        ]
    ];

    // Function to get message based on locale
    public static function detectLocale(): string
    {
        return $_SESSION['user_locale'] ?? substr($_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en', 0, 2);
    }

    public static function get(string $key): string
    {
        $locale = self::detectLocale();
        return self::$messages[$locale][$key] ?? self::$messages['en'][$key] ?? $key;
    }
}

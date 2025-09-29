<?php

return [
    /*
    |--------------------------------------------------------------------------
    | JWT Secret Key
    |--------------------------------------------------------------------------
    |
    | This key is used to sign and verify JWT tokens. It should be the same
    | key used in the API project to ensure compatibility.
    |
    */
    'secret' => env('JWT_SECRET', '12345678901234567890123456789012'),

    /*
    |--------------------------------------------------------------------------
    | JWT Algorithm
    |--------------------------------------------------------------------------
    |
    | The algorithm used to sign the JWT tokens. Must match the API project.
    |
    */
    'algorithm' => 'HS256',

    /*
    |--------------------------------------------------------------------------
    | JWT Token Header Names
    |--------------------------------------------------------------------------
    |
    | The header names to look for JWT tokens in requests.
    |
    */
    'token_headers' => [
        'TOKEN',
        'Authorization'
    ],

    /*
    |--------------------------------------------------------------------------
    | Valid Roles
    |--------------------------------------------------------------------------
    |
    | The roles that are considered valid for authentication.
    | Must match the roles defined in the API project.
    |
    */
    'valid_roles' => [
        'USER',
        'ADMIN',
        'SUPERUSER',
        'DEVELOPER',
        'COMPETITOR',
        'VOLONTEER'
    ],

    /*
    |--------------------------------------------------------------------------
    | Bypass User Agent
    |--------------------------------------------------------------------------
    |
    | User agent string that bypasses JWT validation (for internal service calls).
    |
    */
    'bypass_user_agent' => 'Loppservice/1.0',
];


<?php

error_reporting(E_ALL);

// Should be set to '0' in production
ini_set('display_errors', '1');

// Timezone
date_default_timezone_set('Europe/Stockholm');

// Settings
$settings = [];

// Path settings
$settings['root'] = dirname(__DIR__);


$settings['upload_directory'] = __DIR__ . "/../uploads/";

// Error Handling Middleware settings
$settings['error'] = [

    // Should be set to false in production
    'display_error_details' => true,

    // Parameter is passed to the default ErrorHandler
    // View in rendered output by enabling the "displayErrorDetails" setting.
    // For the console and unit tests we also disable it
    'log_errors' => true,

    // Display error details in error log
    'log_error_details' => true,
];


//// Database settings
$settings['db'] = [
    'driver' => 'mysql',
    'host' => '192.168.1.221',
    'port' => '3310',
    'username' => 'root',
    'database' => 'vasterbottenbrevet_se',
    'password' => 'secret',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'flags' => [
        // Turn off persistent connections
        PDO::ATTR_PERSISTENT => false,
        // Enable exceptions
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,

        PDO::ATTR_AUTOCOMMIT => true,
        // Emulate prepared statements
        PDO::ATTR_EMULATE_PREPARES => true,
        // Set default fetch mode to array
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],
];





//$settings['db'] = [
//    'driver' => 'mysql',
//    'host' => 's513.loopia.se',
//    'port' => '3306',
//    'username' => 'bengt@v308744',
//    'database' => 'vasterbottenbrevet_se_db_1',
//    'password' => 'cykelintresset',
//    'charset' => 'utf8mb4',
//    'collation' => 'utf8mb4_unicode_ci',
//    'flags' => [
//        // Turn off persistent connections
//        PDO::ATTR_PERSISTENT => false,
//        // Enable exceptions
//        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
//        // Emulate prepared statements
//        PDO::ATTR_EMULATE_PREPARES => true,
//        // Set default fetch mode to array
//        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
//    ],
//
//];


$settings['genders'] = ["1"=> ['sv'=> 'Kvinna', 'en' => 'Female'], "2" => ['sv' => 'Man', 'en' => 'Male']];

$settings['secretkey'] = "12345678901234567890123456789012";


$settings['path'] = "/api/";

$settings['demo'] = "true";

$settings['rusaurl'] = 'https://rusa.jkassen.org/time/rusa-time-api.php';

$settings['loppserviceurl'] = 'http://app:80/loppservice';

$settings['apikey'] = 'testkey';

return $settings;
<?php

error_reporting(E_ALL);

// Should be set to '0' in production
ini_set('display_errors', $_ENV['APP_DEBUG'] ?? '1');

// Timezone
date_default_timezone_set('Europe/Stockholm');

// Settings
$settings = [];

// Path settings
$settings['root'] = dirname(__DIR__);
$settings['upload_directory'] = __DIR__ . "/../uploads/";
$settings['path'] = $_ENV['APP_PATH'] ?? "/api/";

// Error Handling Middleware settings
$settings['error'] = [
    // Should be set to false in production
    'display_error_details' => $_ENV['APP_DEBUG'] ?? true,
    'log_errors' => true,
    'log_error_details' => true,
];

// Database settings
$settings['db'] = [
    'driver' => 'mysql',
    'host' => $_ENV['DB_HOST'] ?? '192.168.1.221',
    'port' => $_ENV['DB_PORT'] ?? '3310',
    'username' => $_ENV['DB_USERNAME'] ?? 'root',
    'database' => $_ENV['DB_DATABASE'] ?? 'vasterbottenbrevet_se',
    'password' => $_ENV['DB_PASSWORD'] ?? 'secret',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'flags' => [
        PDO::ATTR_PERSISTENT => false,
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_AUTOCOMMIT => true,
        PDO::ATTR_EMULATE_PREPARES => true,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ],
];

// Email settings
$settings['mail'] = [
    'mail_host' => $_ENV['MAIL_HOST'] ?? 'mailhog',
    'mail_port' => $_ENV['MAIL_PORT'] ?? 1025,
    'mail_username' => $_ENV['MAIL_USERNAME'] ?? '',
    'mail_password' => $_ENV['MAIL_PASSWORD'] ?? '',
    'mail_encryption' => $_ENV['MAIL_ENCRYPTION'] ?? 'tls',
    'mail_from_address' => $_ENV['MAIL_FROM_ADDRESS'] ?? 'noreply@vasterbottenbrevet.se',
    'mail_from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'Västerbottenbrevet'
];

// Application settings
$settings['genders'] = [
    "1" => ['sv'=> 'Kvinna', 'en' => 'Female'],
    "2" => ['sv' => 'Man', 'en' => 'Male']
];

// Security settings
$settings['secretkey'] = $_ENV['SECRET_KEY'] ?? "12345678901234567890123456789012";
$settings['apikey'] = $_ENV['API_KEY'] ?? 'testkey';

// Feature flags
$settings['demo'] = filter_var($_ENV['APP_DEMO'] ?? 'true', FILTER_VALIDATE_BOOLEAN);

// External service URLs
$settings['rusaurl'] = $_ENV['RUSA_URL'] ?? 'https://rusa.jkassen.org/time/rusa-time-api.php';
$settings['loppserviceurl'] = $_ENV['LOPPSERVICE_URL'] ?? 'http://app:80/loppservice';

return $settings;

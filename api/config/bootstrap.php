<?php

use DI\ContainerBuilder;
use Slim\App;
use Dotenv\Dotenv;

require_once __DIR__ . '/../vendor/autoload.php';

// Load .env file
$dotenv = Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->safeLoad();

$containerBuilder = new ContainerBuilder();

// settings
$containerBuilder->addDefinitions(__DIR__ . '/container.php');

// dependency injection
$container = $containerBuilder->build();

// Applikationen
$app = $container->get(App::class);

// Register routes
(require __DIR__ . '/routes.php')($app);

// Register middleware
(require __DIR__ . '/middleware.php')($app);

return $app;

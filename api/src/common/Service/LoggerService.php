<?php

namespace App\common\Service;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Formatter\LineFormatter;
use Psr\Container\ContainerInterface;

class LoggerService
{
    private Logger $logger;
    private ContainerInterface $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->initializeLogger();
    }

    private function initializeLogger(): void
    {
        $settings = $this->container->get('settings');
        $logSettings = $settings['logging'] ?? [];

        $this->logger = new Logger('brevet-api');

        // Create logs directory if it doesn't exist
        $logPath = $logSettings['path'] ?? __DIR__ . '/../../../logs';
        if (!is_dir($logPath)) {
            mkdir($logPath, 0755, true);
        }

        // Main application log with rotation (daily) - similar to Laravel's daily driver
        $mainHandler = new RotatingFileHandler(
            $logPath . '/app.log',
            $logSettings['max_files'] ?? 14, // Same as Laravel default
            $logSettings['level'] ?? Logger::DEBUG
        );
        $mainHandler->setFormatter(new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s'
        ));
        $this->logger->pushHandler($mainHandler);

        // Error log (separate file for errors)
        $errorHandler = new RotatingFileHandler(
            $logPath . '/error.log',
            $logSettings['max_files'] ?? 14,
            Logger::ERROR
        );
        $errorHandler->setFormatter(new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
            'Y-m-d H:i:s'
        ));
        $this->logger->pushHandler($errorHandler);

        // Development: also log to console if debug is enabled
        if (($logSettings['debug'] ?? false) && php_sapi_name() !== 'cli') {
            $consoleHandler = new StreamHandler('php://stdout', Logger::DEBUG);
            $consoleHandler->setFormatter(new LineFormatter(
                "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n",
                'Y-m-d H:i:s'
            ));
            $this->logger->pushHandler($consoleHandler);
        }
    }

    public function getLogger(): Logger
    {
        return $this->logger;
    }

    // Laravel-style convenience methods
    public function emergency(string $message, array $context = []): void
    {
        $this->logger->emergency($message, $context);
    }

    public function alert(string $message, array $context = []): void
    {
        $this->logger->alert($message, $context);
    }

    public function critical(string $message, array $context = []): void
    {
        $this->logger->critical($message, $context);
    }

    public function error(string $message, array $context = []): void
    {
        $this->logger->error($message, $context);
    }

    public function warning(string $message, array $context = []): void
    {
        $this->logger->warning($message, $context);
    }

    public function notice(string $message, array $context = []): void
    {
        $this->logger->notice($message, $context);
    }

    public function info(string $message, array $context = []): void
    {
        $this->logger->info($message, $context);
    }

    public function debug(string $message, array $context = []): void
    {
        $this->logger->debug($message, $context);
    }

    public function log(string $level, string $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
} 
<?php

namespace App\common\Service;

use PDO;
use Psr\Container\ContainerInterface;

class CliContainer implements ContainerInterface
{
    private array $services = [];
    private PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->initializeServices();
    }

    /**
     * Initialize basic services
     */
    private function initializeServices(): void
    {
        // Register basic services
        $this->services['pdo'] = $this->pdo;
        $this->services['logger'] = new CliLoggerService();
        
        // Add settings for LoggerService - use the same logs directory as the app
        $this->services['settings'] = [
            'logging' => [
                'path' => __DIR__ . '/../../logs',
                'level' => \Monolog\Logger::DEBUG,
                'max_files' => 14,
                'debug' => true
            ]
        ];
        
        // Add LoggerService for application logging
        $this->services[\App\common\Service\LoggerService::class] = new \App\common\Service\LoggerService($this);
        
        // You can add more services here as needed
        // $this->services['email_service'] = new EmailService();
        // $this->services['notification_service'] = new NotificationService();
        
        // Register repositories
        // $this->services['participant_repository'] = new ParticipantRepository($this->pdo);
        // $this->services['event_repository'] = new EventRepository($this->pdo);
    }

    /**
     * Get a service from the container
     */
    public function get(string $id)
    {
        if (!$this->has($id)) {
            throw new \Exception("Service '{$id}' not found in container");
        }
        
        return $this->services[$id];
    }

    /**
     * Check if a service exists in the container
     */
    public function has(string $id): bool
    {
        return isset($this->services[$id]);
    }

    /**
     * Register a new service
     */
    public function register(string $id, $service): void
    {
        $this->services[$id] = $service;
    }
} 
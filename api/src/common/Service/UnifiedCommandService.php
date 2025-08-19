<?php

namespace App\common\Service;

use PDO;
use Psr\Container\ContainerInterface;

abstract class UnifiedCommandService
{
    protected PDO $pdo;
    protected CliLoggerService $logger;
    protected ContainerInterface $container;
    protected array $options;

    public function __construct(PDO $pdo, CliLoggerService $logger, ContainerInterface $container, array $options = [])
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
        $this->container = $container;
        $this->options = $options;
    }

    /**
     * Execute the command
     * @return array ['success' => bool, 'message' => string, 'data' => mixed]
     */
    abstract public function execute(): array;

    /**
     * Get command description
     * @return string
     */
    abstract public function getDescription(): string;

    /**
     * Get command name
     * @return string
     */
    abstract public function getName(): string;

    /**
     * Check if command should run based on schedule
     * @return bool
     */
    abstract public function shouldRun(): bool;

    /**
     * Get option value
     * @param string $key
     * @param mixed $default
     * @return mixed
     */
    protected function getOption(string $key, $default = null)
    {
        return $this->options[$key] ?? $default;
    }

    /**
     * Get a service from the container
     * @param string $serviceName
     * @return mixed
     */
    protected function getService(string $serviceName)
    {
        return $this->container->get($serviceName);
    }

    /**
     * Get a repository from the container
     * @param string $repositoryName
     * @return mixed
     */
    protected function getRepository(string $repositoryName)
    {
        return $this->container->get($repositoryName);
    }

    /**
     * Check if a service exists in the container
     * @param string $serviceName
     * @return bool
     */
    protected function hasService(string $serviceName): bool
    {
        return $this->container->has($serviceName);
    }
} 
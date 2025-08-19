<?php

namespace App\Commands;

use App\common\Service\UnifiedCommandService;
use App\common\Service\CliLoggerService;
use App\common\Service\UnifiedSchedulerService;
use PDO;
use Psr\Container\ContainerInterface;

class UnifiedCommandRegistry
{
    private UnifiedSchedulerService $scheduler;
    private PDO $pdo;
    private CliLoggerService $logger;
    private ContainerInterface $container;

    public function __construct(UnifiedSchedulerService $scheduler, PDO $pdo, CliLoggerService $logger, ContainerInterface $container)
    {
        $this->scheduler = $scheduler;
        $this->pdo = $pdo;
        $this->logger = $logger;
        $this->container = $container;
    }

    /**
     * Register all available commands
     * @return void
     */
    public function registerAllCommands(): void
    {
        $commands = [
            \App\Commands\ExampleEveryMinuteCommand::class,
            // Add more commands here as they are created
        ];

        foreach ($commands as $commandClass) {
            $this->registerCommand($commandClass);
        }

        $this->logger->info("Registered " . count($commands) . " unified commands");
    }

    /**
     * Register a specific command
     * @param string $commandClass
     * @param array $options
     * @return void
     */
    public function registerCommand(string $commandClass, array $options = []): void
    {
        try {
            $command = new $commandClass($this->pdo, $this->logger, $this->container, $options);
            $this->scheduler->registerCommand($command);
            $this->logger->info("Registered unified command: " . $command->getName());
        } catch (\Exception $e) {
            $this->logger->error("Failed to register unified command {$commandClass}: " . $e->getMessage());
        }
    }

    /**
     * Get all registered commands
     * @return array
     */
    public function getRegisteredCommands(): array
    {
        return $this->scheduler->getCommands();
    }

    /**
     * Get command by name
     * @param string $name
     * @return UnifiedCommandService|null
     */
    public function getCommand(string $name): ?UnifiedCommandService
    {
        return $this->scheduler->getCommand($name);
    }

    /**
     * Execute a specific command by name
     * @param string $name
     * @return array
     */
    public function executeCommand(string $name): array
    {
        $command = $this->getCommand($name);
        if (!$command) {
            return [
                'success' => false,
                'message' => "Command '{$name}' not found",
                'data' => []
            ];
        }

        return $this->scheduler->executeCommand($command);
    }

    /**
     * List all available commands with their descriptions
     * @return array
     */
    public function listCommands(): array
    {
        $commands = $this->getRegisteredCommands();
        $list = [];

        foreach ($commands as $command) {
            $list[] = [
                'name' => $command->getName(),
                'description' => $command->getDescription(),
                'should_run' => $command->shouldRun()
            ];
        }

        return $list;
    }
} 
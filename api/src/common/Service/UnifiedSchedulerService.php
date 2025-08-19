<?php

namespace App\common\Service;

use PDO;
use Exception;
use Psr\Container\ContainerInterface;

class UnifiedSchedulerService
{
    private PDO $pdo;
    private CliLoggerService $logger;
    private ContainerInterface $container;
    private array $commands = [];

    public function __construct(PDO $pdo, CliLoggerService $logger, ContainerInterface $container)
    {
        $this->pdo = $pdo;
        $this->logger = $logger;
        $this->container = $container;
    }

    /**
     * Register a command
     * @param UnifiedCommandService $command
     * @return void
     */
    public function registerCommand(UnifiedCommandService $command): void
    {
        $this->commands[$command->getName()] = $command;
    }

    /**
     * Get all registered commands
     * @return array
     */
    public function getCommands(): array
    {
        return $this->commands;
    }

    /**
     * Get a specific command by name
     * @param string $name
     * @return UnifiedCommandService|null
     */
    public function getCommand(string $name): ?UnifiedCommandService
    {
        return $this->commands[$name] ?? null;
    }

    /**
     * Run all scheduled commands
     * @return array
     */
    public function runScheduledCommands(): array
    {
        $results = [];
        $this->logger->info("Starting unified scheduled commands execution");

        foreach ($this->commands as $command) {
            if ($command->shouldRun()) {
                $result = $this->executeCommand($command);
                $results[$command->getName()] = $result;
            }
        }

        $this->logger->info("Completed unified scheduled commands execution", ['results' => $results]);
        return $results;
    }

    /**
     * Execute a specific command by name
     * @param string $commandName
     * @return array
     */
    public function executeCommandByName(string $commandName): array
    {
        $command = $this->getCommand($commandName);
        if (!$command) {
            throw new Exception("Command '{$commandName}' not found");
        }
        return $this->executeCommand($command);
    }

    /**
     * Execute a command
     * @param UnifiedCommandService $command
     * @return array
     */
    public function executeCommand(UnifiedCommandService $command): array
    {
        $startTime = microtime(true);
        
        try {
            $this->logger->info("Executing command: {$command->getName()}");
            
            $result = $command->execute();
            $result['execution_time'] = microtime(true) - $startTime;
            
            // Log the execution (but don't save to database for success)
            $this->logger->info("Command {$command->getName()} completed", ['result' => $result]);
            
            return $result;
            
        } catch (Exception $e) {
            $this->logger->error("Error executing command {$command->getName()}: " . $e->getMessage());
            
            $result = [
                'success' => false,
                'message' => $e->getMessage(),
                'execution_time' => microtime(true) - $startTime
            ];
            
            $this->logger->error("Command {$command->getName()} failed", ['result' => $result]);
            
            // Only save to database when there's an error
            $this->recordError($command->getName(), $result);
            
            return $result;
        }
    }

    /**
     * Record command error in database (only for failures)
     * @param string $commandName
     * @param array $result
     * @return void
     */
    private function recordError(string $commandName, array $result): void
    {
        try {
            $sql = "
                INSERT INTO scheduled_commands (command_name, error_message, execution_data)
                VALUES (:command_name, :error_message, :execution_data)
            ";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'command_name' => $commandName,
                'error_message' => $result['message'],
                'execution_data' => json_encode($result)
            ]);
        } catch (Exception $e) {
            $this->logger->error("Failed to record command error: " . $e->getMessage());
        }
    }

    /**
     * Get command error history
     * @param string $commandName
     * @return array
     */
    public function getCommandHistory(string $commandName): array
    {
        try {
            $sql = "SELECT * FROM scheduled_commands WHERE command_name = :command_name ORDER BY failed_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute(['command_name' => $commandName]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logger->error("Failed to get command error history: " . $e->getMessage());
            return [];
        }
    }

    /**
     * List all command error histories
     * @return array
     */
    public function getAllCommandHistories(): array
    {
        try {
            $sql = "SELECT * FROM scheduled_commands ORDER BY failed_at DESC LIMIT 100";
            $stmt = $this->pdo->query($sql);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logger->error("Failed to get all command error histories: " . $e->getMessage());
            return [];
        }
    }
}

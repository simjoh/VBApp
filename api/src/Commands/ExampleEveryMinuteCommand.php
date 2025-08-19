<?php

namespace App\Commands;

use App\common\Service\UnifiedCommandService;
use App\common\Service\CliLoggerService;
use App\common\Service\ScheduleHelper;
use PDO;
use Psr\Container\ContainerInterface;

class ExampleEveryMinuteCommand extends UnifiedCommandService
{


    public function getName(): string
    {
        return 'example:every-minute';
    }

    public function getDescription(): string
    {
        return 'Example command that runs every 2 minutes';
    }

    public function shouldRun(): bool
    {
        // This command runs every 2 minutes
        return ScheduleHelper::everyMinutes(2);
    }

    public function execute(): array
    {
        try {
            $this->logger->info("Executing every 2 minutes command");
            
            $timestamp = date('Y-m-d H:i:s');
            $memoryUsage = memory_get_usage(true);
            $memoryUsageMB = round($memoryUsage / 1024 / 1024, 2);
            
            // Log to console/logs only
            
            $this->logger->info("Every 2 minutes check completed", [
                'timestamp' => $timestamp,
                'memory_usage_mb' => $memoryUsageMB
            ]);
            
            // Also write to application log file
            $this->writeToAppLog($timestamp, $memoryUsageMB);

            return [
                'success' => true,
                'message' => "Every 2 minutes check completed successfully",
                'data' => [
                    'timestamp' => $timestamp,
                    'memory_usage_mb' => $memoryUsageMB
                ]
            ];

        } catch (\Exception $e) {
            $this->logger->error("Error in every minute command: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data' => []
            ];
        }
    }

    private function writeToAppLog(string $timestamp, float $memoryUsageMB): void
    {
        try {
            // Write directly to the application log file
            $logDir = __DIR__ . '/../../logs';
            $logFile = $logDir . '/app-' . date('Y-m-d') . '.log';
            
            $logMessage = sprintf(
                '[%s] brevet-api.INFO: Scheduled command executed {"command":"example:every-2-minutes","timestamp":"%s","memory_usage_mb":%.2f,"status":"success"} []',
                $timestamp,
                $timestamp,
                $memoryUsageMB
            );
            
            file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND | LOCK_EX);
            
        } catch (\Exception $e) {
            $this->logger->warning("Could not write to app log: " . $e->getMessage());
        }
    }

} 
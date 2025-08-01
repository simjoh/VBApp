<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ExampleEveryTwoMinutes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:example-every-two-minutes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Example command that runs every 2 minutes';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $timestamp = now()->format('Y-m-d H:i:s');
            $memoryUsage = memory_get_usage(true);
            $memoryUsageMB = round($memoryUsage / 1024 / 1024, 2);

            // Log to Laravel's log system
            Log::info("Executing every 2 minutes command in loppservice", [
                'timestamp' => $timestamp,
                'memory_usage_mb' => $memoryUsageMB,
                'command' => 'example-every-two-minutes'
            ]);

            $this->info("Every 2 minutes check completed successfully");
            $this->info("Timestamp: {$timestamp}");
            $this->info("Memory Usage: {$memoryUsageMB} MB");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            Log::error("Error in every 2 minutes command: " . $e->getMessage());
            $this->error("Error: " . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

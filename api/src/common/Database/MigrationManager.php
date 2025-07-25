<?php

namespace App\common\Database;

use PDO;
use PDOException;
use Exception;

class MigrationManager
{
    private PDO $connection;
    private string $migrationsPath;
    private string $migrationsTable = 'migrations';

    public function __construct(PDO $connection, string $migrationsPath = null)
    {
        $this->connection = $connection;
        $this->migrationsPath = $migrationsPath ?? __DIR__ . '/../../../database/migrations';
    }

    /**
     * Initialize the migrations table if it doesn't exist
     */
    public function initialize(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->migrationsTable}` (
            `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
            `migration` varchar(255) NOT NULL,
            `batch` int(11) NOT NULL,
            `executed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id`),
            UNIQUE KEY `migration_unique` (`migration`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

        try {
            $this->connection->exec($sql);
            echo "✓ Migrations table initialized\n";
        } catch (PDOException $e) {
            throw new Exception("Failed to create migrations table: " . $e->getMessage());
        }
    }

    /**
     * Get all migration files from the migrations directory
     */
    private function getMigrationFiles(): array
    {
        if (!is_dir($this->migrationsPath)) {
            throw new Exception("Migrations directory not found: {$this->migrationsPath}");
        }

        $files = glob($this->migrationsPath . '/*.sql');
        sort($files); // Ensure files are processed in order
        
        return $files;
    }

    /**
     * Get already executed migrations from database
     */
    private function getExecutedMigrations(): array
    {
        $sql = "SELECT migration FROM {$this->migrationsTable} ORDER BY id";
        $stmt = $this->connection->query($sql);
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }

    /**
     * Get the next batch number
     */
    private function getNextBatch(): int
    {
        $sql = "SELECT MAX(batch) as max_batch FROM {$this->migrationsTable}";
        $stmt = $this->connection->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['max_batch'] ?? 0) + 1;
    }

    /**
     * Extract migration name from filename
     */
    private function getMigrationName(string $filepath): string
    {
        return basename($filepath);
    }

    /**
     * Execute a single migration file
     */
    private function executeMigration(string $filepath, int $batch): bool
    {
        $migrationName = $this->getMigrationName($filepath);
        $sql = file_get_contents($filepath);

        if (empty($sql)) {
            echo "⚠ Warning: Empty migration file: {$migrationName}\n";
            return false;
        }

        // Check if this migration contains temporary tables or complex operations
        $isComplexMigration = $this->isComplexMigration($sql);

        try {
            if ($isComplexMigration) {
                // For complex migrations, don't use transactions
                echo "⚠ Note: Running complex migration without transaction: {$migrationName}\n";
                return $this->executeComplexMigration($filepath, $migrationName, $batch);
            } else {
                // For simple migrations, use transactions
                return $this->executeSimpleMigration($filepath, $migrationName, $batch);
            }
        } catch (PDOException $e) {
            echo "✗ Failed to execute migration {$migrationName}: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Check if migration is complex (contains temporary tables, etc.)
     */
    private function isComplexMigration(string $sql): bool
    {
        $complexPatterns = [
            '/CREATE\s+TEMPORARY\s+TABLE/i',
            '/DROP\s+TEMPORARY\s+TABLE/i',
            '/CREATE\s+TABLE.*TEMPORARY/i',
            '/TRUNCATE\s+TABLE/i',
            '/LOCK\s+TABLES/i',
            '/UNLOCK\s+TABLES/i',
            '/PREPARE\s+stmt\s+FROM/i',
            '/EXECUTE\s+stmt/i',
            '/DEALLOCATE\s+PREPARE/i'
        ];

        foreach ($complexPatterns as $pattern) {
            if (preg_match($pattern, $sql)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Execute simple migration with transaction
     */
    private function executeSimpleMigration(string $filepath, string $migrationName, int $batch): bool
    {
        $sql = file_get_contents($filepath);

        try {
            // Start transaction
            $this->connection->beginTransaction();

            // Execute the migration SQL
            $this->connection->exec($sql);

            // Record the migration as executed
            $insertSql = "INSERT INTO {$this->migrationsTable} (migration, batch) VALUES (?, ?)";
            $stmt = $this->connection->prepare($insertSql);
            $stmt->execute([$migrationName, $batch]);

            // Commit transaction
            $this->connection->commit();

            echo "✓ Executed migration: {$migrationName}\n";
            return true;

        } catch (PDOException $e) {
            // Safely rollback transaction if it exists
            try {
                if ($this->connection->inTransaction()) {
                    $this->connection->rollBack();
                }
            } catch (PDOException $rollbackError) {
                // Ignore rollback errors, just log them
                echo "⚠ Warning: Could not rollback transaction: " . $rollbackError->getMessage() . "\n";
            }
            
            throw $e;
        }
    }

    /**
     * Execute complex migration without transaction
     */
    private function executeComplexMigration(string $filepath, string $migrationName, int $batch): bool
    {
        $sql = file_get_contents($filepath);

        try {
            // Execute the migration SQL without transaction
            $this->connection->exec($sql);

            // Record the migration as executed
            $insertSql = "INSERT INTO {$this->migrationsTable} (migration, batch) VALUES (?, ?)";
            $stmt = $this->connection->prepare($insertSql);
            $stmt->execute([$migrationName, $batch]);

            echo "✓ Executed migration: {$migrationName}\n";
            return true;

        } catch (PDOException $e) {
            throw $e;
        }
    }

    /**
     * Run all pending migrations
     */
    public function migrate(): array
    {
        $this->initialize();

        $migrationFiles = $this->getMigrationFiles();
        $executedMigrations = $this->getExecutedMigrations();
        $nextBatch = $this->getNextBatch();

        $pendingMigrations = array_filter($migrationFiles, function($file) use ($executedMigrations) {
            return !in_array(basename($file), $executedMigrations);
        });

        if (empty($pendingMigrations)) {
            echo "✓ No pending migrations found\n";
            return ['success' => true, 'executed' => 0];
        }

        echo "Found " . count($pendingMigrations) . " pending migration(s)\n";
        echo "Starting batch {$nextBatch}...\n\n";

        $executed = 0;
        $failed = 0;

        foreach ($pendingMigrations as $file) {
            if ($this->executeMigration($file, $nextBatch)) {
                $executed++;
            } else {
                $failed++;
            }
        }

        echo "\nMigration summary:\n";
        echo "- Executed: {$executed}\n";
        echo "- Failed: {$failed}\n";

        return [
            'success' => $failed === 0,
            'executed' => $executed,
            'failed' => $failed
        ];
    }

    /**
     * Show migration status
     */
    public function status(): void
    {
        $this->initialize();

        $migrationFiles = $this->getMigrationFiles();
        $executedMigrations = $this->getExecutedMigrations();

        echo "Migration Status:\n";
        echo str_repeat("-", 80) . "\n";

        foreach ($migrationFiles as $file) {
            $migrationName = $this->getMigrationName($file);
            $status = in_array($migrationName, $executedMigrations) ? "✓ Executed" : "⏳ Pending";
            echo sprintf("%-60s %s\n", $migrationName, $status);
        }

        echo str_repeat("-", 80) . "\n";
        echo "Total: " . count($migrationFiles) . " migrations\n";
        echo "Executed: " . count($executedMigrations) . "\n";
        echo "Pending: " . (count($migrationFiles) - count($executedMigrations)) . "\n";
    }

    /**
     * Rollback the last batch of migrations
     */
    public function rollback(int $steps = 1): array
    {
        $this->initialize();

        // Get the last batch number
        $sql = "SELECT MAX(batch) as max_batch FROM {$this->migrationsTable}";
        $stmt = $this->connection->query($sql);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $lastBatch = $result['max_batch'] ?? 0;

        if ($lastBatch === 0) {
            echo "✓ No migrations to rollback\n";
            return ['success' => true, 'rolled_back' => 0];
        }

        // Get migrations from the last batch
        $sql = "SELECT migration FROM {$this->migrationsTable} WHERE batch = ? ORDER BY id DESC";
        $stmt = $this->connection->prepare($sql);
        $stmt->execute([$lastBatch]);
        $migrationsToRollback = $stmt->fetchAll(PDO::FETCH_COLUMN);

        echo "Rolling back batch {$lastBatch} (" . count($migrationsToRollback) . " migrations)...\n";

        $rolledBack = 0;
        $failed = 0;

        foreach ($migrationsToRollback as $migration) {
            if ($this->rollbackMigration($migration)) {
                $rolledBack++;
            } else {
                $failed++;
            }
        }

        echo "\nRollback summary:\n";
        echo "- Rolled back: {$rolledBack}\n";
        echo "- Failed: {$failed}\n";

        return [
            'success' => $failed === 0,
            'rolled_back' => $rolledBack,
            'failed' => $failed
        ];
    }

    /**
     * Rollback a single migration (removes from migrations table)
     * Note: This doesn't reverse the SQL changes, just removes the record
     */
    private function rollbackMigration(string $migrationName): bool
    {
        try {
            $sql = "DELETE FROM {$this->migrationsTable} WHERE migration = ?";
            $stmt = $this->connection->prepare($sql);
            $stmt->execute([$migrationName]);

            echo "✓ Rolled back migration: {$migrationName}\n";
            return true;

        } catch (PDOException $e) {
            echo "✗ Failed to rollback migration {$migrationName}: " . $e->getMessage() . "\n";
            return false;
        }
    }

    /**
     * Create a new migration file
     */
    public function createMigration(string $name): string
    {
        $timestamp = date('Y_m_d_His');
        $filename = "{$timestamp}_{$name}.sql";
        $filepath = $this->migrationsPath . '/' . $filename;

        if (!is_dir($this->migrationsPath)) {
            mkdir($this->migrationsPath, 0755, true);
        }

        $template = "-- Migration: {$name}\n";
        $template .= "-- Date: " . date('Y-m-d H:i:s') . "\n";
        $template .= "-- Description: {$name}\n\n";
        $template .= "-- Add your SQL here\n";
        $template .= "-- Example:\n";
        $template .= "-- ALTER TABLE `table_name` ADD COLUMN `new_column` varchar(255) NOT NULL;\n";

        file_put_contents($filepath, $template);

        echo "✓ Created migration: {$filename}\n";
        return $filepath;
    }
} 
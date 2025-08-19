<?php

namespace App\Action\Infra;

use App\common\Service\LoggerService;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LogsAction
{
    private LoggerService $logger;
    private ContainerInterface $container;

    public function __construct(LoggerService $logger, ContainerInterface $container)
    {
        $this->logger = $logger;
        $this->container = $container;
    }

    public function __invoke(Request $request, Response $response, array $args): Response
    {
        $settings = $this->container->get('settings');
        $logPath = $settings['logging']['path'] ?? __DIR__ . '/../../../../logs';
        
        $action = $args['action'] ?? 'list';
        
        switch ($action) {
            case 'list':
                return $this->listLogFiles($response, $logPath);
            case 'read':
                return $this->readLogFile($request, $response, $logPath);
            case 'tail':
                return $this->tailLogFile($request, $response, $logPath);
            default:
                return $this->errorResponse($response, 'Invalid action. Use: list, read, or tail', 400);
        }
    }

    private function listLogFiles(Response $response, string $logPath): Response
    {
        if (!is_dir($logPath)) {
            return $this->errorResponse($response, 'Log directory not found', 404);
        }

        $files = [];
        $logFiles = glob($logPath . '/*.log');

        foreach ($logFiles as $file) {
            $filename = basename($file);
            $files[] = [
                'filename' => $filename,
                'size' => filesize($file),
                'modified' => date('Y-m-d H:i:s', filemtime($file)),
                'path' => $file
            ];
        }

        $data = [
            'log_directory' => $logPath,
            'files' => $files,
            'total_files' => count($files)
        ];

        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function readLogFile(Request $request, Response $response, string $logPath): Response
    {
        $queryParams = $request->getQueryParams();
        $filename = $queryParams['file'] ?? null;
        $lines = (int)($queryParams['lines'] ?? 100);
        $level = $queryParams['level'] ?? null;

        if (!$filename) {
            return $this->errorResponse($response, 'File parameter is required', 400);
        }

        // Security: only allow .log files
        if (!preg_match('/^[a-zA-Z0-9\-_]+\.log$/', $filename)) {
            return $this->errorResponse($response, 'Invalid filename', 400);
        }

        $filePath = $logPath . '/' . $filename;

        if (!file_exists($filePath)) {
            return $this->errorResponse($response, 'Log file not found', 404);
        }

        $logEntries = $this->parseLogFile($filePath, $lines, $level);

        $data = [
            'filename' => $filename,
            'file_path' => $filePath,
            'total_lines' => count($logEntries),
            'requested_lines' => $lines,
            'filter_level' => $level,
            'entries' => $logEntries
        ];

        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function tailLogFile(Request $request, Response $response, string $logPath): Response
    {
        $queryParams = $request->getQueryParams();
        $filename = $queryParams['file'] ?? null;
        $lines = (int)($queryParams['lines'] ?? 50);

        if (!$filename) {
            return $this->errorResponse($response, 'File parameter is required', 400);
        }

        // Security: only allow .log files
        if (!preg_match('/^[a-zA-Z0-9\-_]+\.log$/', $filename)) {
            return $this->errorResponse($response, 'Invalid filename', 400);
        }

        $filePath = $logPath . '/' . $filename;

        if (!file_exists($filePath)) {
            return $this->errorResponse($response, 'Log file not found', 404);
        }

        $logEntries = $this->tailLogFileContent($filePath, $lines);

        $data = [
            'filename' => $filename,
            'file_path' => $filePath,
            'total_lines' => count($logEntries),
            'requested_lines' => $lines,
            'entries' => $logEntries
        ];

        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }

    private function parseLogFile(string $filePath, int $maxLines, ?string $levelFilter): array
    {
        $entries = [];
        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        if ($lines === false) {
            return $entries;
        }

        // Get the last N lines
        $lines = array_slice($lines, -$maxLines);

        foreach ($lines as $line) {
            $parsed = $this->parseLogLine($line);
            
            if ($parsed) {
                // Apply level filter if specified
                if ($levelFilter && strtolower($parsed['level']) !== strtolower($levelFilter)) {
                    continue;
                }
                
                $entries[] = $parsed;
            }
        }

        return $entries;
    }

    private function tailLogFileContent(string $filePath, int $lines): array
    {
        $entries = [];
        $file = new \SplFileObject($filePath);
        $file->seek(PHP_INT_MAX);
        $totalLines = $file->key();
        
        $startLine = max(0, $totalLines - $lines);
        $file->seek($startLine);
        
        while (!$file->eof()) {
            $line = $file->current();
            if ($line) {
                $parsed = $this->parseLogLine($line);
                if ($parsed) {
                    $entries[] = $parsed;
                }
            }
            $file->next();
        }

        return $entries;
    }

    private function parseLogLine(string $line): ?array
    {
        // Parse Monolog format: [2025-08-01 10:31:57] brevet-api.INFO: message {"context"} []
        if (preg_match('/^\[([^\]]+)\]\s+([^.]+)\.([A-Z]+):\s+(.+?)(?:\s+(\{.*\})\s+(\[.*\]))?$/', $line, $matches)) {
            $timestamp = $matches[1];
            $channel = $matches[2];
            $level = $matches[3];
            $message = $matches[4];
            $context = isset($matches[5]) ? json_decode($matches[5], true) : [];
            $extra = isset($matches[6]) ? json_decode($matches[6], true) : [];

            return [
                'timestamp' => $timestamp,
                'channel' => $channel,
                'level' => $level,
                'message' => $message,
                'context' => $context,
                'extra' => $extra,
                'raw_line' => $line
            ];
        }

        // If it doesn't match the expected format, return as raw line
        return [
            'timestamp' => null,
            'channel' => null,
            'level' => null,
            'message' => $line,
            'context' => [],
            'extra' => [],
            'raw_line' => $line
        ];
    }

    private function errorResponse(Response $response, string $message, int $statusCode): Response
    {
        $data = [
            'error' => $message,
            'status_code' => $statusCode
        ];

        $response->getBody()->write(json_encode($data, JSON_PRETTY_PRINT));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($statusCode);
    }
} 
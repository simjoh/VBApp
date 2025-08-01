# Logging System Documentation

## Overview

The API uses Monolog for logging, providing the same powerful logging capabilities as Laravel but without the Laravel dependencies. The logging system automatically logs all incoming requests and responses, and provides easy-to-use methods for application logging.

## Features

- **Automatic Request Logging**: All incoming requests and responses are automatically logged with timing information
- **Daily Log Rotation**: Log files are rotated daily to manage disk space
- **Multiple Log Levels**: Separate files for general logs and error logs
- **Structured Logging**: Support for context arrays with additional data
- **Laravel-style API**: Familiar logging methods similar to Laravel's Log facade

## Configuration

The logging system is configured in `config/settings.php`:

```php
$settings['logging'] = [
    'path' => __DIR__ . '/../logs',
    'level' => \Monolog\Logger::DEBUG,
    'max_files' => 14, // Keep 14 days of logs
    'debug' => filter_var($_ENV['APP_DEBUG'] ?? 'true', FILTER_VALIDATE_BOOLEAN),
];
```

## Usage

### In Actions and Services

```php
use App\common\Service\LoggerService;

class SomeAction
{
    private LoggerService $logger;

    public function __construct(LoggerService $logger)
    {
        $this->logger = $logger;
    }

    public function someMethod()
    {
        // Different log levels
        $this->logger->debug('Debug information', ['user_id' => 123]);
        $this->logger->info('General information', ['action' => 'user_login']);
        $this->logger->warning('Warning message', ['deprecated_feature' => 'old_api']);
        $this->logger->error('Error occurred', ['error' => $e->getMessage()]);
        $this->logger->critical('Critical error', ['system' => 'database_down']);
    }
}
```

### Available Log Levels

- `emergency()` - System is unusable
- `alert()` - Action must be taken immediately
- `critical()` - Critical conditions
- `error()` - Error conditions
- `warning()` - Warning conditions
- `notice()` - Normal but significant conditions
- `info()` - Informational messages
- `debug()` - Debug-level messages

### Structured Logging

All logging methods support context arrays for additional data:

```php
$this->logger->info('User action performed', [
    'user_id' => $userId,
    'action' => 'participant_created',
    'track_id' => $trackId,
    'timestamp' => time()
]);
```

## Log Files

The system creates two main log files:

1. **`logs/app-YYYY-MM-DD.log`** - Contains all log levels (debug, info, warning, error, etc.)
2. **`logs/error-YYYY-MM-DD.log`** - Contains only error-level and above logs

## Automatic Request Logging

The `RequestLoggingMiddleware` automatically logs:

- **Incoming requests**: Method, URI, IP address, User-Agent
- **Completed requests**: Method, URI, status code, response time

Example log entries:
```
[2025-08-01 10:31:57] brevet-api.INFO: Incoming request {"method":"GET","uri":"http://localhost:8090/api/test/logging","ip":"172.18.0.1","user_agent":"curl/8.12.1"} []
[2025-08-01 10:31:57] brevet-api.INFO: Request completed {"method":"GET","uri":"http://localhost:8090/api/test/logging","status_code":200,"response_time_ms":1.47} []
```

## Testing the Logging System

You can test the logging system by calling the test endpoint:

```bash
curl -X GET -H "APIKEY: notsecret_developer_key" "http://localhost:8090/api/test/logging"
```

This will generate various log entries demonstrating different log levels and structured logging.

## Best Practices

1. **Use appropriate log levels**: Don't use `error` for normal operations
2. **Include context**: Always include relevant data in context arrays
3. **Don't log sensitive data**: Avoid logging passwords, tokens, or personal information
4. **Use structured logging**: Prefer context arrays over string concatenation
5. **Monitor log files**: Regularly check log files for issues and performance

## Integration with Existing Code

To add logging to existing actions or services:

1. Add `LoggerService` to the constructor
2. Inject it via dependency injection
3. Use the appropriate log level for your use case

Example:
```php
// In container.php
\App\Action\SomeAction::class => function (ContainerInterface $container) {
    return new \App\Action\SomeAction(
        $container->get(\App\common\Service\LoggerService::class)
    );
} 
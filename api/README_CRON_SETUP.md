# Cron Setup - Clean Version

This setup uses the existing cron service in docker-compose to run scheduled commands using the unified scheduler framework.

## Current Setup

### Single Command
- **`example:every-minute`** - Runs every 2 minutes and creates a log entry in the application log

### Docker Cron Service
The cron service is configured in `docker/docker-compose.yml` and runs:
```bash
* * * * * root cd /var/www/html/api && /usr/local/bin/php -f unified_cron_scheduler.php >> /var/log/cron.log 2>&1
```

### Logging
The command creates log entries in the application log file (`logs/app-YYYY-MM-DD.log`) with the same format as other application logs.

## Running the System

### 1. Start Docker Services
```bash
cd docker
docker compose up -d
```

### 2. Check Cron Logs
```bash
# Check cron container logs
docker logs vbapp-cron

# Check cron log file
docker exec vbapp-cron cat /var/log/cron.log
```

### 3. Manual Testing
```bash
# Test the command manually
cd api
php artisan schedule:execute example:every-minute

# Test the unified scheduler
php unified_cron_scheduler.php

# List available commands
php artisan schedule:list
```

### 4. API Routes
The schedule can also be managed via API routes:

```bash
# List all commands
curl -X GET -H "APIKEY: notsecret_developer_key" "http://localhost:8090/api/infra/schedule/list"

# Run all scheduled commands
curl -X GET -H "APIKEY: notsecret_developer_key" "http://localhost:8090/api/infra/schedule/run"

# Execute a specific command
curl -X GET -H "APIKEY: notsecret_developer_key" "http://localhost:8090/api/infra/schedule/execute?command=example:every-minute"

# Get execution history
curl -X GET -H "APIKEY: notsecret_developer_key" "http://localhost:8090/api/infra/schedule/history"
```

## Adding New Commands

1. Create a new command class in `src/Commands/`
2. Extend `UnifiedCommandService`
3. Add to `UnifiedCommandRegistry::registerAllCommands()`
4. The command will automatically run based on its `shouldRun()` method

## Framework Integration

The unified scheduler integrates with your existing framework:
- Uses the same database connection
- Follows the same logging patterns
- Integrates with dependency injection container
- Maintains execution tracking

The cron service runs every minute and executes any commands that should run based on their scheduling logic. 
# Logs API Documentation

## Overview

The Logs API provides infrastructure endpoints for reading and managing log files in JSON format. These endpoints are available in the `/api/infra/logs/` section and require admin authentication.

## Authentication

All endpoints require:
- `APIKEY` header: `notsecret_developer_key`
- `TOKEN` header: Valid JWT token with admin privileges

## Endpoints

### 1. List Log Files

**GET** `/api/infra/logs/list`

Lists all available log files with metadata.

**Response:**
```json
{
    "log_directory": "/var/www/html/api/config/../logs",
    "files": [
        {
            "filename": "app-2025-08-01.log",
            "size": 1337,
            "modified": "2025-08-01 10:35:20",
            "path": "/var/www/html/api/config/../logs/app-2025-08-01.log"
        },
        {
            "filename": "error-2025-08-01.log",
            "size": 177,
            "modified": "2025-08-01 10:31:57",
            "path": "/var/www/html/api/config/../logs/error-2025-08-01.log"
        }
    ],
    "total_files": 2
}
```

### 2. Read Log File

**GET** `/api/infra/logs/read`

Reads a specific log file with optional filtering.

**Query Parameters:**
- `file` (required): Log filename (e.g., `app-2025-08-01.log`)
- `lines` (optional): Number of lines to read from the end (default: 100)
- `level` (optional): Filter by log level (DEBUG, INFO, WARNING, ERROR, etc.)

**Examples:**

Read last 10 lines:
```
GET /api/infra/logs/read?file=app-2025-08-01.log&lines=10
```

Read only ERROR level logs:
```
GET /api/infra/logs/read?file=app-2025-08-01.log&lines=50&level=ERROR
```

**Response:**
```json
{
    "filename": "app-2025-08-01.log",
    "file_path": "/var/www/html/api/config/../logs/app-2025-08-01.log",
    "total_lines": 5,
    "requested_lines": 5,
    "filter_level": null,
    "entries": [
        {
            "timestamp": "2025-08-01 10:35:12",
            "channel": "brevet-api",
            "level": "INFO",
            "message": "Incoming request",
            "context": {
                "method": "POST",
                "uri": "http://localhost:8090/api/login",
                "ip": "172.18.0.1",
                "user_agent": "curl/8.12.1"
            },
            "extra": [],
            "raw_line": "[2025-08-01 10:35:12] brevet-api.INFO: Incoming request {\"method\":\"POST\",\"uri\":\"http://localhost:8090/api/login\",\"ip\":\"172.18.0.1\",\"user_agent\":\"curl/8.12.1\"} []"
        }
    ]
}
```

### 3. Tail Log File

**GET** `/api/infra/logs/tail`

Gets the last N lines of a log file (similar to Unix `tail` command).

**Query Parameters:**
- `file` (required): Log filename (e.g., `app-2025-08-01.log`)
- `lines` (optional): Number of lines to read from the end (default: 50)

**Example:**
```
GET /api/infra/logs/tail?file=app-2025-08-01.log&lines=10
```

**Response:**
```json
{
    "filename": "app-2025-08-01.log",
    "file_path": "/var/www/html/api/config/../logs/app-2025-08-01.log",
    "total_lines": 3,
    "requested_lines": 3,
    "entries": [
        {
            "timestamp": "2025-08-01 10:35:51",
            "channel": "brevet-api",
            "level": "INFO",
            "message": "Incoming request",
            "context": {
                "method": "GET",
                "uri": "http://localhost:8090/api/infra/logs/tail?file=app-2025-08-01.log&lines=3",
                "ip": "172.18.0.1",
                "user_agent": "curl/8.12.1"
            },
            "extra": [],
            "raw_line": "[2025-08-01 10:35:51] brevet-api.INFO: Incoming request {\"method\":\"GET\",\"uri\":\"http://localhost:8090/api/infra/logs/tail?file=app-2025-08-01.log&lines=3\",\"ip\":\"172.18.0.1\",\"user_agent\":\"curl/8.12.1\"} []"
        }
    ]
}
```

## Log Entry Structure

Each log entry contains:

- `timestamp`: ISO datetime of the log entry
- `channel`: Logger channel name (usually "brevet-api")
- `level`: Log level (DEBUG, INFO, WARNING, ERROR, etc.)
- `message`: The log message
- `context`: Structured data associated with the log entry
- `extra`: Additional metadata
- `raw_line`: Original log line for reference

## Available Log Files

- `app-YYYY-MM-DD.log`: General application logs (all levels)
- `error-YYYY-MM-DD.log`: Error-level logs only

## Usage Examples

### Using curl

```bash
# Get a token first
TOKEN=$(curl -s -X POST -H "Content-Type: application/json" \
  -H "APIKEY: notsecret_developer_key" \
  -d '{"username":"bethem92@gmail.com","password":"cessna172"}' \
  http://localhost:8090/api/login | jq -r '.token')

# List log files
curl -X GET \
  -H "APIKEY: notsecret_developer_key" \
  -H "TOKEN: $TOKEN" \
  "http://localhost:8090/api/infra/logs/list"

# Read last 20 lines of app log
curl -X GET \
  -H "APIKEY: notsecret_developer_key" \
  -H "TOKEN: $TOKEN" \
  "http://localhost:8090/api/infra/logs/read?file=app-2025-08-01.log&lines=20"

# Get only ERROR logs
curl -X GET \
  -H "APIKEY: notsecret_developer_key" \
  -H "TOKEN: $TOKEN" \
  "http://localhost:8090/api/infra/logs/read?file=app-2025-08-01.log&lines=100&level=ERROR"

# Tail the error log
curl -X GET \
  -H "APIKEY: notsecret_developer_key" \
  -H "TOKEN: $TOKEN" \
  "http://localhost:8090/api/infra/logs/tail?file=error-2025-08-01.log&lines=10"
```

### Using JavaScript/Fetch

```javascript
const token = 'your-jwt-token';

// List log files
const response = await fetch('/api/infra/logs/list', {
  headers: {
    'APIKEY': 'notsecret_developer_key',
    'TOKEN': token
  }
});

const logFiles = await response.json();

// Read specific log file
const logResponse = await fetch('/api/infra/logs/read?file=app-2025-08-01.log&lines=50&level=ERROR', {
  headers: {
    'APIKEY': 'notsecret_developer_key',
    'TOKEN': token
  }
});

const logData = await logResponse.json();
```

## Security Features

- **File validation**: Only `.log` files are allowed
- **Path traversal protection**: Filenames are validated against regex pattern
- **Authentication required**: All endpoints require valid admin token
- **Read-only access**: No write operations are allowed

## Error Responses

```json
{
    "error": "File parameter is required",
    "status_code": 400
}
```

```json
{
    "error": "Log file not found",
    "status_code": 404
}
```

```json
{
    "error": "Invalid filename",
    "status_code": 400
}
```

## Best Practices

1. **Use appropriate line limits**: Don't request too many lines at once for large log files
2. **Filter by level**: Use the `level` parameter to focus on specific log levels
3. **Monitor error logs**: Regularly check error logs for issues
4. **Use tail for real-time monitoring**: Use the tail endpoint for monitoring recent activity
5. **Cache tokens**: Reuse JWT tokens instead of requesting new ones for each call 
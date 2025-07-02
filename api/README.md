# VBApp API Backend

This is the backend API for the VBApp (Västerbottenbrevet) application, built with PHP and Slim framework.

## Quick Start

### Local Development Server
```bash
# Navigate to the API directory
cd api

# Start the development server
php -S localhost:8090 -t public
```

## Database Migrations

The application includes a Laravel-style migration system for managing database schema changes.

### Directory Structure
```
api/
├── database/
│   ├── migrations/     # SQL migration files
│   └── seeders/        # Database seeders (future use)
├── src/
│   ├── common/Database/
│   │   └── MigrationManager.php  # Core migration logic
│   └── Action/Migration/
│       └── MigrationAction.php   # API endpoints
├── artisan             # Command line tool
└── config/
    ├── container.php   # Dependency injection
    └── routes.php      # API routes
```

### Configuration

The migration system uses environment variables for database configuration. These are loaded from the `.env_dev` file in the API directory.

#### Environment Variables
```ini
# Database Configuration
DB_HOST=192.168.1.221      # App database host
DB_PORT=3310               # App database port
DB_DATABASE=vasterbottenbrevet_se  # App database name
DB_USERNAME=root           # Database username
DB_PASSWORD=secret         # Database password
```

### Usage

#### Command Line (Recommended)

The migration system provides an artisan-like command line interface:

```bash
# Navigate to the API directory
cd api

# Run all pending migrations
php artisan migrate

# Check migration status
php artisan migrate:status

# Rollback the last batch
php artisan migrate:rollback

# Rollback specific number of steps
php artisan migrate:rollback --steps=2

# Create a new migration
php artisan migrate:create my_new_migration

# Initialize migrations table
php artisan migrate:init
```

#### API Endpoints

The migration system also provides REST API endpoints for integration with other systems:

**Base URL:** `http://localhost:8090/api/infra/migrations/{action}`

**Required Headers:**
- `APIKEY: notsecret_developer_key`
- `TOKEN: <jwt_token>` (obtained from `/api/login`)

**Available Actions:**

1. **Status** - Check migration status
   ```bash
   curl -X GET "http://localhost:8090/api/infra/migrations/status" \
     -H "APIKEY: notsecret_developer_key" \
     -H "TOKEN: <your_jwt_token>"
   ```

2. **Migrate** - Run pending migrations
   ```bash
   curl -X GET "http://localhost:8090/api/infra/migrations/migrate" \
     -H "APIKEY: notsecret_developer_key" \
     -H "TOKEN: <your_jwt_token>"
   ```

3. **Rollback** - Rollback migrations
   ```bash
   curl -X GET "http://localhost:8090/api/infra/migrations/rollback?steps=1" \
     -H "APIKEY: notsecret_developer_key" \
     -H "TOKEN: <your_jwt_token>"
   ```

4. **Init** - Initialize migrations table
   ```bash
   curl -X GET "http://localhost:8090/api/infra/migrations/init" \
     -H "APIKEY: notsecret_developer_key" \
     -H "TOKEN: <your_jwt_token>"
   ```

### Migration Files

Migration files are SQL scripts with timestamped names in the format: `YYYY_MM_DD_HHMMSS_description.sql`

Example: `2025_01_27_000000_add_dns_dnf_timestamps_to_participant.sql`

### Features

- **Transaction Safety**: Migrations run in transactions with automatic rollback on failure
- **Complex Migration Support**: Handles migrations with temporary tables and complex SQL
- **Batch Management**: Groups migrations into batches for rollback operations
- **Duplicate Prevention**: Tracks executed migrations to prevent re-running
- **Error Handling**: Comprehensive error reporting and recovery
- **API Integration**: REST endpoints for CI/CD integration

### Troubleshooting

#### Database Connection Issues

Make sure your database configuration is correct in `.env_dev`:

```ini
# App Database (vasterbottenbrevet_se)
DB_HOST=192.168.1.221
DB_PORT=3310
DB_DATABASE=vasterbottenbrevet_se
DB_USERNAME=root
DB_PASSWORD=secret
```

#### Permission Issues

- Ensure the database user has sufficient privileges
- Check that the migrations table can be created
- Verify network connectivity to the database

#### Migration Failures

- Check the migration SQL syntax
- Ensure no conflicting schema changes
- Review the error messages for specific issues
- Use the rollback feature to undo failed migrations

### Authentication

To use the API endpoints, you need to:

1. **Get a JWT Token:**
   ```bash
   curl -X POST -H "Content-Type: application/json" \
     -H "APIKEY: notsecret_developer_key" \
     -d '{"username":"bethem92@gmail.com","password":"cessna172"}' \
     http://localhost:8090/api/login
   ```

2. **Use the token in subsequent requests:**
   ```bash
   curl -X GET "http://localhost:8090/api/infra/migrations/status" \
     -H "APIKEY: notsecret_developer_key" \
     -H "TOKEN: <jwt_token_from_login>"
   ```

## Development

### Adding New Migrations

1. Create a new migration file:
   ```bash
   php artisan migrate:create add_new_feature
   ```

2. Edit the generated SQL file in `api/database/migrations/`

3. Run the migration:
   ```bash
   php artisan migrate
   ```

### Testing Migrations

- Use the status endpoint to verify migration state
- Test rollback functionality before deploying
- Check database schema after migration execution
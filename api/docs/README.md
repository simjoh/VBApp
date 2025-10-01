# VBApp API Backend

PHP-based API backend for the VBApp (Västerbottenbrevet) application, built with Slim framework.

## Overview

This is the backend API for the VBApp (Västerbottenbrevet) application, providing comprehensive functionality for managing cycling events, participants, and event administration.

## Features

- **Event Management**: Complete event lifecycle management
- **Participant Registration**: Handle participant registrations and tracking
- **Database Migrations**: Laravel-style migration system
- **Authentication**: JWT-based authentication system
- **API Documentation**: RESTful API endpoints
- **Cron Jobs**: Automated task scheduling

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

### Usage

#### Command Line (Recommended)

```bash
# Navigate to the API directory
cd api

# Run all pending migrations
php artisan migrate

# Check migration status
php artisan migrate:status

# Rollback the last batch
php artisan migrate:rollback
```

## Authentication

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

## License

This project is licensed under the MIT License.
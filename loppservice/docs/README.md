# Loppservice API

Laravel-based API service for managing cycling events and registrations.

## Overview

The Loppservice API provides comprehensive functionality for managing cycling events, participant registrations, and event statistics. Built with Laravel framework, it offers a robust and scalable solution for event management.

## Features

- **Event Management**: Create, update, and manage cycling events (MSR, BP, BRM)
- **Participant Registration**: Handle participant registrations and reservations
- **Event Statistics**: Real-time statistics and analytics
- **Optional Products**: Manage event-related products and services
- **Stripe Integration**: Payment processing for registrations and products
- **API Documentation**: Comprehensive API documentation

## Quick Start

### Prerequisites

- PHP 8.1+
- Composer
- MySQL/PostgreSQL
- Docker (for containerized deployment)

### Installation

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd loppservice
   ```

2. **Install dependencies**
   ```bash
   composer install
   ```

3. **Environment setup**
   ```bash
   cp .env.example .env
   # Edit .env with your database and configuration settings
   ```

4. **Database setup**
   ```bash
   php artisan migrate
   php artisan db:seed
   ```

5. **Start the development server**
   ```bash
   php artisan serve
   ```

## API Documentation

- [Event API Documentation](API_DOCUMENTATION.md) - Complete API reference for event management endpoints
- [Stripe API Documentation](STRIPE_API.md) - Comprehensive Stripe integration API reference

## Configuration

### Environment Variables

Key environment variables for configuration:

```env
# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=loppservice
DB_USERNAME=root
DB_PASSWORD=

# Stripe
STRIPE_KEY=your_stripe_public_key
STRIPE_SECRET=your_stripe_secret_key

# Application
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost:8000
```

## Development

### Running Tests

```bash
php artisan test
```

### Database Migrations

```bash
# Create a new migration
php artisan make:migration create_example_table

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback
```

## License

This project is licensed under the MIT License.

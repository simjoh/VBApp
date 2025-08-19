#!/bin/bash
set -e

# Fix permissions for uploads directory
if [ -d "/var/www/html/api/uploads" ]; then
    echo "Setting permissions for uploads directory..."
    chown -R www-data:www-data /var/www/html/api/uploads
    chmod -R 755 /var/www/html/api/uploads
    echo "Permissions set successfully"
fi

# Create uploads directory if it doesn't exist
if [ ! -d "/var/www/html/api/uploads" ]; then
    echo "Creating uploads directory..."
    mkdir -p /var/www/html/api/uploads
    chown -R www-data:www-data /var/www/html/api/uploads
    chmod -R 755 /var/www/html/api/uploads
    echo "Uploads directory created successfully"
fi

# Fix permissions for logs directory
if [ -d "/var/www/html/api/logs" ]; then
    echo "Setting permissions for logs directory..."
    chown -R www-data:www-data /var/www/html/api/logs
    chmod -R 755 /var/www/html/api/logs
    echo "Logs permissions set successfully"
fi

# Create logs directory if it doesn't exist
if [ ! -d "/var/www/html/api/logs" ]; then
    echo "Creating logs directory..."
    mkdir -p /var/www/html/api/logs
    chown -R www-data:www-data /var/www/html/api/logs
    chmod -R 755 /var/www/html/api/logs
    echo "Logs directory created successfully"
fi

# Execute the main command
exec "$@" 
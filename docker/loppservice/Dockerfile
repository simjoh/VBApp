# Build stage
FROM composer:2.4 AS build
COPY loppservice/ /app/
WORKDIR /app

# Debug: Show contents of the build context
RUN echo "Contents of /app during build stage:" && ls -al /app

# Install dependencies
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# Development stage
FROM php:8.1-apache-buster AS dev
ENV APP_ENV=dev
ENV APP_DEBUG=true
ENV COMPOSER_ALLOW_SUPERUSER=1

# Install necessary dependencies
RUN apt-get update && apt-get install -y zip && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install pdo pdo_mysql

RUN echo "Contents of loppservice directory before COPY:" && ls -al /app


# Copy app files and dependencies
COPY loppservice/ /var/www/html
COPY --from=build /app/vendor /var/www/html/vendor
COPY docker/loppservice/apache/000-default.dev.conf /etc/apache2/sites-available/000-default.conf
COPY docker/loppservice/environment/env.dev /var/www/html/.env

# Debug: Verify contents after copying files
RUN echo "Contents of /var/www/html after COPY:" && ls -al /var/www/html

# Ensure necessary directories exist and set permissions
RUN mkdir -p /var/www/html/resources/images /var/www/html/storage/logs && \
    chmod -R 777 /var/www/html/storage /var/www/html/storage/logs /var/www/html/resources/images && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite

# Debug: Verify permissions and ownership
RUN echo "Permissions of /var/www/html/storage:" && ls -ld /var/www/html/storage && \
    echo "Permissions of /var/www/html/resources/images:" && ls -ld /var/www/html/resources/images

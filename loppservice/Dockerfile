# Build stage
FROM composer:2.4 AS build
WORKDIR /app
COPY loppservice/ /app/
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

# Base image for dev and production
FROM php:8.1-apache-buster AS base

# Install common dependencies and PHP extensions
RUN apt-get update && apt-get install -y zip vim libxml2-dev && \
    docker-php-ext-install pdo pdo_mysql xml dom && \
    a2enmod rewrite

# Development stage
FROM base AS dev

ENV APP_ENV=dev
ENV APP_DEBUG=true
ENV COMPOSER_ALLOW_SUPERUSER=1

# Copy app files and install dev dependencies
COPY loppservice/ /var/www/html
COPY --from=build /usr/bin/composer /usr/bin/composer
RUN composer install --prefer-dist --no-interaction

# Copy Apache config and environment file
COPY ./docker/loppservice/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/loppservice/enviroment/env.dev /var/www/html/applikation/.env.dev

# Set permissions
RUN mkdir -p /var/www/html/storage /var/www/html/storage/logs /var/www/html/resources/images && \
    chmod -R 777 /var/www/html/storage /var/www/html/storage/logs /var/www/html/resources/images && \
    chown -R www-data:www-data /var/www/

# Production stage
FROM base AS production

ENV APP_ENV=production
ENV APP_DEBUG=false

# Copy production-specific files
COPY ./docker/loppservice/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/loppservice/enviroment/env.prod /var/www/html/.env
COPY --from=build /app/applikation/ /var/www/html

# Set permissions for production
RUN mkdir -p /var/www/html/storage && \
    chmod -R 777 /var/www/html/storage && \
    chown -R www-data:www-data /var/www/

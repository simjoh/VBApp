FROM composer:2.4 as build
COPY loppservice/ /app/
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

FROM php:8.1-apache-buster as dev

ENV APP_ENV=dev
ENV APP_DEBUG=true
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y zip
RUN docker-php-ext-install pdo pdo_mysql

COPY loppservice/ /var/www/html
COPY --from=build /usr/bin/composer /usr/bin/composer
RUN composer install --prefer-dist --no-interaction

COPY ./docker/loppservice/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/loppservice/enviroment/env.dev /var/www/html/applikation/.env.dev

RUN  chmod 777 -R /var/www/html/storage/ && \
     chmod 777 -R /var/www/html/storage/logs/ && \
     chmod 777 -R /var/www/html/resources/images/ && \
     chown -R  www-data:www-data /var/www/ && \
    a2enmod rewrite

FROM php:8.1-apache-buster as production

ENV APP_ENV=production
ENV APP_DEBUG=false

RUN docker-php-ext-configure opcache --enable-opcache && \
    docker-php-ext-install pdo pdo_mysql
COPY ./docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY --from=build ./applikation/ /var/www/html
COPY docker/loppservice/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/loppservice/enviroment/env.prod /var/www/html/.env

RUN chmod 777 -R /var/www/html/storage/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite

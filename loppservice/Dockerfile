FROM composer:2.4 as build
COPY loppservice/ /app/
RUN composer install --prefer-dist --no-dev --optimize-autoloader --no-interaction

FROM php:8.2-apache-buster as dev

ENV APP_ENV=dev
ENV APP_DEBUG=true
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y \
    zip \
    vim \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    default-mysql-client \
    tzdata \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    xml \
    dom \
    zip \
    gd \
    opcache

COPY loppservice/ /var/www/html
COPY --from=build /usr/bin/composer /usr/bin/composer
RUN composer install --prefer-dist --no-interaction

COPY ./docker/loppservice/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/loppservice/enviroment/env.dev /var/www/html/.env
COPY ./loppservice/docker/wait-for-db.sh /usr/local/bin/wait-for-db.sh
RUN chmod +x /usr/local/bin/wait-for-db.sh

RUN chmod 777 -R /var/www/html/storage/ && \
    chmod 777 -R /var/www/html/bootstrap/cache/ && \
    chmod 777 -R /var/www/html/resources/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite

ENV TZ=Europe/Stockholm
RUN ln -snf /usr/share/zoneinfo/Europe/Stockholm /etc/localtime && echo "Europe/Stockholm" > /etc/timezone

RUN echo "date.timezone=Europe/Stockholm" > /usr/local/etc/php/conf.d/timezone.ini

FROM php:8.2-apache-buster as production

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV COMPOSER_ALLOW_SUPERUSER=1

RUN apt-get update && apt-get install -y \
    libxml2-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    default-mysql-client \
    tzdata \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-configure opcache --enable-opcache \
    && docker-php-ext-install -j$(nproc) \
    pdo \
    pdo_mysql \
    xml \
    dom \
    zip \
    gd \
    opcache

COPY ./docker/php/conf.d/opcache.ini /usr/local/etc/php/conf.d/opcache.ini

COPY --from=build /app/ /var/www/html
COPY ./docker/loppservice/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY ./docker/loppservice/enviroment/env.prod /var/www/html/.env
COPY ./loppservice/docker/wait-for-db.sh /usr/local/bin/wait-for-db.sh
RUN chmod +x /usr/local/bin/wait-for-db.sh

RUN chmod 777 -R /var/www/html/storage/ && \
    chmod 777 -R /var/www/html/bootstrap/cache/ && \
    chown -R www-data:www-data /var/www/ && \
    a2enmod rewrite

ENV TZ=Europe/Stockholm
RUN ln -snf /usr/share/zoneinfo/Europe/Stockholm /etc/localtime && echo "Europe/Stockholm" > /etc/timezone

RUN echo "date.timezone=Europe/Stockholm" > /usr/local/etc/php/conf.d/timezone.ini

# Add healthcheck
HEALTHCHECK --interval=30s --timeout=3s \
    CMD curl -f http://localhost/ || exit 1

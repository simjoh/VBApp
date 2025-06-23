FROM php:8.1-apache

# Install system dependencies in a single RUN command to reduce image layers
RUN apt-get update && apt-get install -y --no-install-recommends \
    cron \
    g++ \
    gettext \
    libicu-dev \
    openssl \
    libc-client-dev \
    libkrb5-dev \
    libxml2-dev \
    libfreetype6-dev \
    libgd-dev \
    libbz2-dev \
    libtidy-dev \
    libcurl4-openssl-dev \
    libz-dev \
    libmemcached-dev \
    libxslt-dev \
    git \
    libpq-dev \
    libzip-dev \
    libwebp-dev && \
    apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-install zip bcmath bz2 calendar dba exif gettext iconv intl soap tidy xsl mysqli pgsql pdo pdo_mysql pdo_pgsql && \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp && \
    docker-php-ext-install gd && \
    docker-php-ext-configure imap --with-kerberos --with-imap-ssl && \
    docker-php-ext-install imap && \
    docker-php-ext-configure hash --with-mhash && \
    pecl install xdebug mongodb redis && \
    docker-php-ext-enable xdebug mongodb redis

# Xdebug configuration
RUN echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
    echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Apache configuration
RUN a2enmod setenvif headers rewrite || true

# Set working directory (optional but recommended)
WORKDIR /var/www/html

# Create uploads directory and set permissions
RUN mkdir -p /var/www/html/api/uploads && \
    chown -R www-data:www-data /var/www/html/api/uploads && \
    chmod -R 755 /var/www/html/api/uploads

# Create entrypoint script to fix permissions on container start
COPY docker/apache/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
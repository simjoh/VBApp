FROM php:8.1-fpm-alpine
USER root
RUN docker-php-ext-install mysqli pdo pdo_mysql && docker-php-ext-enable pdo_mysql

CMD ["php-fpm"]

EXPOSE 9000
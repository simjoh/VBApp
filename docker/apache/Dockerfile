FROM php:7.4-apache
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli
RUN docker-php-ext-install pdo pdo_mysql && docker-php-ext-enable pdo_mysql
RUN docker-php-ext-install ext ext_zip && docker-php-ext-enable ext_zip
RUN apt-get update && apt-get upgrade -y

RUN a2enmod rewrite
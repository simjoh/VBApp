#FROM php:8.1-fpm
#
#RUN apt-get update
#RUN apt-get install -y cron
#RUN apt-get install -y less
#
#COPY scheduler /etc/cron.d/crontab
#
#RUN touch /var/log/cron.log
#
#
## owner can read and write into the crontab, group and others can read it
#RUN chmod 0644 /etc/cron.d/crontab
#
## running our crontab using the binary from the package we installed
#RUN chmod 0644 /usr/bin/crontab /etc/cron.d/crontab
#
#RUN service cron start -f
#CMD ["cron", "-f"]

FROM php:8.2-apache

# Updating packages and installing cron
RUN apt-get update && apt-get install cron -y


RUN apt-get update && apt-get install -y \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install pdo_mysql

# Adding crontab to the appropriate location
ADD scheduler /etc/cron.d/my-cron-file

# Giving permission to crontab file
RUN chmod 0644 /etc/cron.d/my-cron-file

RUN touch /var/log/cron.log

# Running crontab
RUN crontab /etc/cron.d/my-cron-file

# Creating entry point for cron
ENTRYPOINT ["cron", "-f"]
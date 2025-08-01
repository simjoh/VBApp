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

FROM ubuntu:22.04

# Install cron and curl
RUN apt-get update && apt-get install -y cron curl

# Set working directory
WORKDIR /var/www/html

# Adding crontab to the appropriate location
ADD scheduler /etc/cron.d/my-cron-file

# Copy the API call script
COPY call_schedule_api.sh /var/www/html/call_schedule_api.sh

# Giving permission to crontab file and script
RUN chmod 0644 /etc/cron.d/my-cron-file && \
    chmod +x /var/www/html/call_schedule_api.sh

RUN touch /var/log/cron.log

# Running crontab
RUN crontab /etc/cron.d/my-cron-file

# Creating entry point for cron
ENTRYPOINT ["cron", "-f"]
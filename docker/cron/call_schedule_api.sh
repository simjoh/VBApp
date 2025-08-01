#!/bin/bash

# Call the API schedule endpoint to run all scheduled commands
echo "Calling API schedule at $(date)" >> /var/log/cron.log

# Call the main API (brevet-api)
echo "Calling brevet-api schedule..." >> /var/log/cron.log
curl -s -X GET "http://php-apache/api/infra/schedule/run" \
  -H "APIKEY: notsecret_developer_key" \
  -H "Content-Type: application/json" \
  -H "User-Agent: Loppservice/1.0" \
  >> /var/log/cron.log 2>&1

# Call the loppservice API
echo "Calling loppservice schedule..." >> /var/log/cron.log
curl -s -X GET "http://vbapp-app-1/loppservice/api/artisan/command/schedule/run" \
  -H "apikey: testkey" \
  -H "Content-Type: application/json" \
  -H "User-Agent: Loppservice/1.0" \
  >> /var/log/cron.log 2>&1

echo "Schedule APIs called at $(date)" >> /var/log/cron.log 
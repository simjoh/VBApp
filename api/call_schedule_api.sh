#!/bin/bash

# Call the API schedule endpoint to run all scheduled commands
echo "Calling API schedule at $(date)" >> /var/log/cron.log
curl -s -X GET "http://php-apache/api/infra/schedule/run" \
  -H "APIKEY: notsecret_developer_key" \
  -H "Content-Type: application/json" \
  >> /var/log/cron.log 2>&1

echo "Schedule API called at $(date)" >> /var/log/cron.log 
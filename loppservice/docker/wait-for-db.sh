#!/bin/sh
# wait-for-db.sh

set -e

host="$1"
shift
cmd="$@"

echo "Waiting for MySQL..."
until MYSQL_PWD=secret mysql -h "$host" -u "myuser" -e 'SELECT 1' &> /dev/null; do
  echo "MySQL is unavailable - sleeping"
  sleep 1
done

echo "MySQL is up - executing command"
exec $cmd

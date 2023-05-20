#!/bin/bash

SCRIPTS_DIR=$( cd -- "$( dirname -- "${BASH_SOURCE[0]}" )" &> /dev/null && pwd )

TUNNEL_PORT=3305

set -o allexport
source "$SCRIPTS_DIR"/.env.database
set +o allexport

mysqladmin ping -h 127.0.0.1 -P$TUNNEL_PORT --silent
MYSQL_TUNNEL_IS_RUNNING=$?

if [[ $MYSQL_TUNNEL_IS_RUNNING -ne 0 ]]; then
    echo "=> Creating tunnel to production"
    vapor database:tunnel vapor $TUNNEL_PORT &
fi

echo "=> Waiting for mysql to be available"
while ! mysqladmin ping -h 127.0.0.1 -P$TUNNEL_PORT --silent; do
    sleep 1
done

echo "=> Downloading mysqldump from production"
mysqldump --set-gtid-purged=OFF -h 127.0.0.1 -P$TUNNEL_PORT -u "$USERNAME" -p"$PASSWORD" "$DATABASE" > "$SCRIPTS_DIR"/database.sql

echo "=> Importing mysqldump"
mysql -h 127.0.0.1 -u sail -ppassword garden_planner < "$SCRIPTS_DIR"/database.sql

echo "=> Cleaning up"
rm "$SCRIPTS_DIR"/database.sql

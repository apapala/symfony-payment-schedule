#!/usr/bin/env sh

XDEBUG_MODE=off composer install

for i in $(seq 1 10); do
    echo "[$(date)] Starting messenger consumer $i/10"
    php bin/console messenger:consume async -vv --memory-limit=128M --time-limit=300
    sleep 2
done

#!/usr/bin/env sh

XDEBUG_MODE=off composer install -n -o --apcu-autoloader --no-scripts --prefer-dist --dev
php bin/console doctrine:migrations:migrate

php-fpm
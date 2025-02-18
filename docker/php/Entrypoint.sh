#!/usr/bin/env sh

XDEBUG_MODE=off composer install

php bin/console doctrine:migrations:migrate

php-fpm
#!/bin/sh

su www -c 'php artisan config:cache'
su www -c 'php artisan migrate'

su www -c 'php-fpm7 -F' &
nginx -g 'daemon off;' &

wait -n
exit $?

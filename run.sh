#!/bin/sh

su-exec www-data php artisan config:cache
su-exec www-data php artisan migrate --force

php-fpm -F &
nginx -g 'daemon off;' &

wait -n
exit $?

# This scripts starts both php-fpm and nginx. If one exits, it kills the other
# one and exits, stopping the container (which could then be restarted)

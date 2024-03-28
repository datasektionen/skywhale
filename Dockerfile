FROM alpine:3.15

RUN apk --no-cache add php7 php7-mbstring php7-pdo php7-openssl php7-json php7-phar php7-fileinfo \
    php7-dom php7-tokenizer php7-xml php7-xmlwriter php7-session php7-pgsql php7-pdo_pgsql php7-fpm php7-curl \
    git zip nginx

RUN php -r 'copy("https://getcomposer.org/installer", "php://stdout");' | \
    php -- --install-dir=/usr/local/bin --filename=composer --1 --quiet
# Composer version 2 does not work with the version of laravel we're using

COPY nginx_app.conf /etc/nginx/http.d/default.conf
RUN sed -i 's/;clear_env/clear_env/' /etc/php7/php-fpm.d/www.conf # Gives PHP access to environment variables

RUN : \
    && addgroup -S www \
    && adduser -D -H -G www www \
    && chown www:www /var/log/php7 \
    && :

WORKDIR /app
COPY . /app

# With cache, build goes brrrrrr
# Unless specifying `--no-scripts`, someone thought it would be funny to connect to the database
# right after installing dependencies.
RUN --mount=type=cache,target=/app/vendor/ composer install --no-scripts --no-dev && cp -r vendor _vendor
RUN ln -s _vendor vendor && chown -R www:www /app

EXPOSE 8000

USER root
CMD ["/app/run.sh"]

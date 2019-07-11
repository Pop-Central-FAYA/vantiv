FROM ulsmith/alpine-apache-php7
LABEL maintainer="Joshua Adeyemi <joshua@fayamedia.com>"

### Default PHP INI Environment variables ###
### Modify for the environment needed ###
### See https://github.com/php/php-src for development and production php ini recommended values
### Below are the production values (set them to be default, this is to account for cases where
### these values might be accidentally left unset in production)
ENV PHP_SHORT_OPEN_TAG=On \
    PHP_OUTPUT_BUFFERING=4096 \
    PHP_MAX_INPUT_TIME=60 \
    PHP_ERROR_REPORTING="E_ALL & ~E_DEPRECATED & ~E_STRICT" \
    PHP_DISPLAY_ERRORS=Off \
    PHP_DISPLAY_STARTUP_ERRORS=Off \
    PHP_LOG_ERRORS=On \
    PHP_VARIABLES_ORDER=GPCS \
    PHP_REQUEST_ORDER=GP \
    PHP_REGISTER_ARGC_ARGV=Off \
    PHP_SESSION_GC_DIVISOR=1000 \
    PHP_SESSION_SID_BITS_PER_CHARACTER=5 \
    PHP_ERROR_LOG=/dev/stderr \
    PHP_DATE_TIMEZONE=UTC

# This forces apache logs to redirect to stdout so they go through the docker logging
RUN ln -sf /proc/self/fd/1 /var/log/apache2/access.log && \
    ln -sf /proc/self/fd/1 /var/log/apache2/error.log

# We do a rm at the end in case some packages were cached from the parent image
RUN apk add --no-cache --update php7-fileinfo \
    mysql-client \
    curl \
    nodejs \
    npm \
    && rm -vrf /var/cache/apk/*

COPY . /app

WORKDIR /app

### Setup the custom php configs (they should be read last)
COPY ./100-php.ini ./101-php-overrides.ini /etc/php7/conf.d/

RUN mv /app/.env.example /app/.env \
    && rm -rf /app/vendor \
    && rm -rf /app/dev \
    && rm -rf /app/storage \
    && rm -rf /app/bootstrap/cache \
    && rm -rf /app/node_modules \
    && bash -c 'mkdir -p /app/storage/{app,framework,logs,debugbar}' \
    && bash -c 'mkdir -p /app/storage/framework/{sessions,views,cache,testing}' \
    && bash -c 'mkdir -p /app/bootstrap/cache'

### Need to install docker in production without the dev dependencies (get that fixed)
# RUN /usr/local/bin/composer install --no-ansi --no-dev --optimize-autoloader --no-plugins --no-scripts --no-interaction \
RUN /usr/local/bin/composer install --no-ansi --optimize-autoloader --no-plugins --no-interaction \
    && rm -rf /app/node_modules \
    && php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:clear \
    && chown -R apache:apache /app \
    && chmod -R 755 /app


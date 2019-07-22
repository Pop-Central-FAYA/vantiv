# Inspired by https://github.com/TrafeX/docker-php-nginx/blob/master/Dockerfile
# Also helpful: https://medium.com/@shakyShane/laravel-docker-part-2-preparing-for-production-9c6a024e9797
FROM alpine:3.9
LABEL Maintainer="Joshua Adeyemi <joshua@fayamedia.com>" \
      Description="Lightweight container with Nginx 1.14 & PHP-FPM 7.2 based on Alpine Linux."

# Install system packages
RUN apk --no-cache update \
    && apk --no-cache upgrade \
    && apk --no-cache add ca-certificates \
    openssl \ 
    openssh \
    nginx \
    supervisor \
    curl \
    nodejs \
    npm \
    bash \
    git \
    tzdata \
    openntpd \
    nano \
    php7 \
    mysql-client
    
# Add PHP Packages
RUN apk --no-cache add \
    php7-fpm \
    php7-mysqli \
    php7-json \
    php7-openssl \
    php7-curl \
    php7-zlib \
    php7-xml \
    php7-phar \
    php7-intl \
    php7-dom \
    php7-xmlreader \
    php7-ctype \
    php7-mbstring \
    php7-gd \
    php7-iconv \
	php7-ftp \
	php7-xdebug \
	php7-mcrypt \
	php7-mbstring \
	php7-soap \
	php7-gmp \
	php7-pdo_odbc \
	php7-dom \
	php7-pdo \
	php7-zip \
	php7-mysqli \
	php7-sqlite3 \
	php7-pdo_pgsql \
	php7-bcmath \
	php7-gd \
	php7-odbc \
	php7-pdo_mysql \
	php7-pdo_sqlite \
	php7-gettext \
	php7-xml \
	php7-xmlreader \
	php7-xmlwriter \
	php7-tokenizer \
	php7-xmlrpc \
	php7-bz2 \
	php7-pdo_dblib \
	php7-curl \
	php7-ctype \
	php7-session \
	php7-redis \
	php7-exif \
	php7-intl \
	php7-fileinfo \
	php7-ldap \
	php7-apcu \
    php7-simplexml

# Add Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

# Setup document root
RUN mkdir -p /var/www
WORKDIR /var/www

# The different settings files needed by the services, plus changing the owner of the settings files to nobody
COPY build/nginx.conf /etc/nginx/nginx.conf
COPY build/fpm-pool.conf /etc/php7/php-fpm.d/www.conf
COPY build/php.ini /etc/php7/conf.d/zzz_custom.ini
COPY build/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN chown -R nobody.nobody /run \
    && chown -R nobody.nobody /var/lib/nginx \ 
    && chown -R nobody.nobody /var/tmp/nginx \
    && chown -R nobody.nobody /var/log/nginx

# Add application
COPY --chown=nobody . /var/www/

# Install the php and npm dependencies
RUN /usr/local/bin/composer install --no-ansi --optimize-autoloader --no-plugins --no-interaction \
    && rm -rf /var/www/node_modules \
    && php artisan config:clear \
    && php artisan cache:clear \
    && php artisan route:clear

RUN cp /var/www/.env.example /var/www/.env

# Switch to use a non-root user from here on
USER nobody

# Expose the port nginx is reachable on
EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]

# Configure a healthcheck to validate that everything is up&running
HEALTHCHECK --timeout=10s CMD curl --silent --fail http://127.0.0.1:8080/fpm-ping

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
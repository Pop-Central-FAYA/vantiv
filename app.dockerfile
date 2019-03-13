FROM ulsmith/alpine-apache-php7
MAINTAINER Joshua Adeyemi <joshua@fayamedia.com>

WORKDIR /app

RUN apk add php7-fileinfo mysql-client php7-intl

# This forces apache logs to redirect to stdout so they go through the docker logging
RUN ln -sf /proc/self/fd/1 /var/log/apache2/access.log && \
    ln -sf /proc/self/fd/1 /var/log/apache2/error.log

#Add curl to the docker
RUN apk add curl

COPY . /app
COPY ./.env.example /app/.env

RUN rm -rf /app/vendor \
    && rm -rf /app/dev \
    && rm -rf /app/storage \
    && rm -rf /app/bootstrap/cache \
    && bash -c 'mkdir -p /app/storage/{app,framework,logs,debugbar}' \
    && bash -c 'mkdir -p /app/storage/framework/{sessions,views,cache,testing}' \
    && mkdir -p /app/bootstrap/cache \
    && composer install \
    && chown -R apache:apache /app

FROM ulsmith/alpine-apache-php7
MAINTAINER Joshua Adeyemi <joshua@fayamedia.com>

WORKDIR /app

RUN apk add php7-fileinfo mysql-client

# This forces apache logs to redirect to stdout so they go through the docker logging
RUN ln -sf /proc/self/fd/1 /var/log/apache2/access.log && \
    ln -sf /proc/self/fd/1 /var/log/apache2/error.log

#Add curl to the docker
RUN apk add curl

COPY . /app
COPY ./.env.example /app/.env

# Delete some folders that might have been copied over
RUN rm -rf /app/vendor \
    && rm -rf /app/dev \
    && composer install \
    && find /app/bootstrap/cache -type f -delete \
    && find /app/storage/app -type f -delete \
    && find /app/storage/framework -type f -delete \
    && find /app/storage/logs -type f -delete \
    && find /app/storage/debugbar -type f -delete \
    && chmod -R 777 /app/bootstrap/cache \
    && chmod -R 777 /app/storage/ \
    && chmod -R 777 /app/public

FROM ulsmith/alpine-apache-php7
MAINTAINER Joshua Adeyemi <joshua@fayamedia.com>

WORKDIR /app

RUN apk add php7-fileinfo

COPY . /app

# Delete some folders that might have been copied over
RUN rm -rf /app/vendor \
    && rm -rf /app/bootstrap/cache \
    && mkdir -p /app/bootstrap/cache \
    && composer install \
    && chown -R apache:apache /app

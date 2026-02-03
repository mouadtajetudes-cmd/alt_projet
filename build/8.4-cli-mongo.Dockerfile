FROM php:8.4-cli

RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip zip curl cron openssl \
    && rm -rf /var/lib/apt/lists/*

RUN curl -sSLf -o /usr/local/bin/install-php-extensions \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions

RUN install-php-extensions \
    gettext iconv intl tidy zip sockets \
    mysqli pdo_mysql \
    mongodb \
    @composer

RUN install-php-extensions xdebug

WORKDIR /var/php
EXPOSE 80

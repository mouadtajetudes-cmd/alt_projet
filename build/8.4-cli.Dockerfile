FROM php:8.4-cli

# System deps
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip zip curl cron openssl \
    libpq-dev \
    && rm -rf /var/lib/apt/lists/*

# PHP extensions installer
RUN curl -sSLf -o /usr/local/bin/install-php-extensions \
    https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions \
    && chmod +x /usr/local/bin/install-php-extensions

# PHP extensions (ajuste selon besoin)
RUN install-php-extensions \
    gettext iconv intl tidy zip sockets \
    mysqli pdo_pgsql \
    @composer \
    xdebug
    

WORKDIR /var/php
EXPOSE 80

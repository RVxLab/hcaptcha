FROM composer:2 as composer
FROM node:16-alpine as node
FROM php:7.4-cli-alpine

ARG PUID=1000
ARG PGID=1000

# Install needed packages
RUN apk add -u --no-cache \
    curl \
    zip unzip \
    git \
    libpng-dev \
    python2 \
    autoconf g++ make

# Install PHP extensions
RUN pecl install pcov \
    && docker-php-ext-enable pcov

# Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

# Node
COPY --from=node /usr/local/bin/node /usr/local/bin/node
COPY --from=node /usr/local/lib/node_modules /usr/local/lib/node_modules
RUN ln -s /usr/local/lib/node_modules/npm/bin/npm-cli.js /usr/local/bin/npm \
    && ln -s /usr/local/lib/node_modules/npm/bin/npx-cli.js /usr/local/bin/npx

# Set up non-root
RUN addgroup -g $PGID app \
    && adduser -D -h /home/app -G app -u $PUID app

# Custom PHP config
COPY php.ini /usr/local/etc/php/conf.d/

# Create directory for code coverage within PhpStorm
RUN mkdir /opt/phpstorm-coverage && chown app:app /opt/phpstorm-coverage

# Let's do this
USER app
WORKDIR /var/www/html

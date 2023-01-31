FROM composer:2 as composer
FROM php:8.2-cli-alpine

ARG PUID=1000
ARG PGID=1000

# Install needed packages
RUN apk add -u --no-cache $PHPIZE_DEPS curl zip unzip
# Install PHP extensions
RUN pecl install pcov \
    && docker-php-ext-enable pcov

# Composer
COPY --from=composer /usr/bin/composer /usr/bin/composer

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

FROM php:8.3-fpm-alpine

# Bash
RUN apk add --no-cache bash

# Set defaults for variables used by run.sh
ENV COMPOSER_HOME=/root/.composer

COPY --from=mlocati/php-extension-installer /usr/bin/install-php-extensions /usr/local/bin/

# Install dependencies
RUN install-php-extensions zip pgsql pdo_pgsql bcmath sockets xdebug xml mbstring

# Create Composer directory (cache and auth files) & Get Composer
RUN mkdir -p $COMPOSER_HOME \
    && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copy PHP script into container
COPY src /var/www/src

# Set working directory
WORKDIR /var/www/src
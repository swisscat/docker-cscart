ARG PHP_BUILD_VERSION=7.1
FROM php:$PHP_BUILD_VERSION-fpm

MAINTAINER Etienne Favre <etienne.favre@b-i.com>

ARG PHP_BUILD_APCU_VERSION=5.1.8

RUN pecl install xdebug \
    && apt-get update -y \
    && apt-get install -y \
      libxml2-dev \
      libfreetype6-dev \
      libjpeg62-turbo-dev \
      libpng12-dev \
      php-soap \
    && apt-get clean -y \
    && docker-php-ext-enable xdebug apcu \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install mysqli soap gd zip

# Set www-data as owner for /var/www
RUN chown -R www-data:www-data /var/www/
RUN chmod -R g+w /var/www/

# Create log folders
RUN mkdir /var/log/php-fpm && \
    touch /var/log/php-fpm/access.log && \
    touch /var/log/php-fpm/error.log && \
    chown -R www-data:www-data /var/log/php-fpm

ENV PHP_INI_SCAN_DIR=:/usr/local/etc/php/conf.d/custom.d
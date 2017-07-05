FROM php:5.6-fpm

MAINTAINER Etienne Favre <etienne.favre@b-i.com>

RUN pecl install xdebug-2.5.5 \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install mysqli

# Set www-data as owner for /var/www
RUN chown -R www-data:www-data /var/www/
RUN chmod -R g+w /var/www/

# Create log folders
RUN mkdir /var/log/php-fpm && \
    touch /var/log/php-fpm/access.log && \
    touch /var/log/php-fpm/error.log && \
    chown -R www-data:www-data /var/log/php-fpm
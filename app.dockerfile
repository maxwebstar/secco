FROM php:7-fpm

RUN apt-get update && apt-get install -y libmcrypt-dev \
    mysql-client libmagickwand-dev --no-install-recommends \
    && pecl install imagick \
    && docker-php-ext-enable imagick \
    && docker-php-ext-install pdo_mysql

RUN apt-get update && \
    apt-get install -y \
         zlib1g-dev \
         && docker-php-ext-install zip \
         && docker-php-ext-install gd

RUN pecl install mongodb \
    && docker-php-ext-enable mongodb
#  && echo 'extention=mongodb' > /usr/local/etc/php/conf.d/mongo.ini

COPY custom.ini /usr/local/etc/php/conf.d/custom.ini
FROM php:7.4-fpm-alpine

ADD ./docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf

RUN addgroup -g 1000 laravel && adduser -G laravel -g laravel -s /bin/sh -D laravel

RUN mkdir -p /var/www/html

RUN chown laravel:laravel /var/www/html

WORKDIR /var/www/html

#
RUN apk add openldap-dev
#RUN apk add autoconf
#RUN apk add build-base
#RUN apk add gcc
#RUN apk add php7-mysqlnd

RUN docker-php-ext-install pdo pdo_mysql
RUN docker-php-ext-install ldap
#
#RUN pecl install mongodb

#
#RUN echo "extension=mongodb.so" >> /usr/local/etc/php/conf.d/docker-php-ext-mongodb.ini
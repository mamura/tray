FROM php:8.3-fpm-alpine

RUN apk add --no-cache \
    bash git unzip libzip-dev icu-dev oniguruma-dev \
    mysql-client curl libpng-dev libxml2-dev autoconf build-base

RUN docker-php-ext-install pdo pdo_mysql intl mbstring zip opcache
RUN docker-php-ext-install pcntl

RUN pecl install redis \
    && docker-php-ext-enable redis

COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

RUN { \
  echo "memory_limit=512M"; \
  echo "upload_max_filesize=32M"; \
  echo "post_max_size=32M"; \
} > /usr/local/etc/php/conf.d/laravel.ini

WORKDIR /var/www

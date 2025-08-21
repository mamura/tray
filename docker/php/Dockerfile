# docker/php/Dockerfile
FROM php:8.3-fpm-alpine

ENV TZ=America/Fortaleza

# 1) Pacotes necessários
RUN apk add --no-cache \
    bash git unzip curl tzdata \
    icu-dev libzip-dev oniguruma-dev \
    $PHPIZE_DEPS \
 && ln -snf /usr/share/zoneinfo/$TZ /etc/localtime \
 && echo $TZ > /etc/timezone

# 2) Extensões PHP necessárias ao Laravel
RUN docker-php-ext-configure intl \
 && docker-php-ext-install -j"$(nproc)" \
      intl pdo_mysql mbstring zip bcmath opcache pcntl

# 2.1) PCOV (coverage leve para PHPUnit)
RUN pecl install pcov \
 && docker-php-ext-enable pcov \
 && printf "pcov.enabled=0\npcov.directory=/var/www\n" > /usr/local/etc/php/conf.d/zz-pcov.ini

# 3) (Opcional) Remover deps de build e manter libs de runtime para reduzir a imagem
RUN apk add --no-cache icu-libs libzip oniguruma \
 && apk del --no-cache $PHPIZE_DEPS icu-dev libzip-dev oniguruma-dev

# 4) Composer
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer

# 5) Ajustes PHP
RUN { \
  echo "memory_limit=512M"; \
  echo "upload_max_filesize=32M"; \
  echo "post_max_size=32M"; \
  echo "date.timezone=${TZ}"; \
} > /usr/local/etc/php/conf.d/laravel.ini

WORKDIR /var/www
